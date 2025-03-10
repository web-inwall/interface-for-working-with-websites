jQuery(document).ready(($) => {
    function cloudflare(func, row = 0, callback = function () {
    }) {
        let $table = $('.sites__data');
        let $row = $table.find('tbody tr').eq(row);
        if (!$row.length) return

        if (row === 0) {
            $table.find('.result_checkbox').prop("indeterminate", false);
            $table.find('.result_checkbox').prop("checked", false);
        }

        let count_rows = $table.find('tbody tr').length;

        let $cell = $row.find('td');
        let $res = $row.find('.result_checkbox');
        let site = {};
        site.domain = $cell.eq(0).find('input').val();
        site.email = $cell.eq(1).find('input').val();
        site.pass = $cell.eq(2).find('input').val();
        site.api = $cell.eq(3).find('input').val();
        site.ip = $cell.eq(4).find('input').val();

        $res.prop("indeterminate", false);
        $res.prop("checked", false);

        $row.addClass('loading');

        $.ajax({
            type: "POST",
            url: `/includes/cf_${func}.php`,
            data: site,
        }).then((response) => {
            // console.log(site.domain, response);
            let next_row = row + 1;
            let last_item = count_rows == next_row;

            if (response) {
                $res.prop("indeterminate", false);
                $res.prop("checked", true);


            } else {
                $res.prop("indeterminate", true);
                $res.prop("checked", false);
            }

            callback(site, response, last_item);

            $row.removeClass('loading');
            cloudflare(func, next_row, callback);
        });
    }

    /* Меню */
    (() => {
        let pathname = location.pathname;
        let $menu_item;

        if (pathname == '/') $menu_item = $(`header ul.main_menu li.nav-item:first-child > a.nav-link`);
        else $menu_item = $(`header ul.main_menu a.nav-link[href="${pathname}"]`);

        if (!$menu_item) return false;

        $menu_item.addClass('active');
    })();

    /* Импорт CSV */
    (() => {
        let $table = $('.sites__data tbody');
        if (!$table.length) return false;

        let $input = $('[name="import-csv-file"]');
        if (!$input.length) return false;

        $(document).on('click', '#startImportCSV', (e) => {
            e.preventDefault();
            $input.click();
        });

        $input.change((e) => {
            var file = e.target.files[0]; // Получаем выбранный файл
            if (file) {
                Papa.parse(file, {
                    header: true, // Указываем, что первая строка содержит заголовки
                    complete: function (results) {
                        var csvData = results.data; // Получаем данные CSV

                        csvData.forEach(row => {
                            let $tr = $(`<tr></tr>`);

                            for (let key in row) {
                                $tr.append($(`<td><input class="form-control" data-key="${key}" type="text" value="${row[key]}"></td>`));
                            }
                            $tr.append($('<td class="text-center"><input class="form-check-input result_checkbox" disabled type="checkbox" value=""></td>'))

                            $table.append($tr);
                        });

                    },
                    error: function (error) {
                        console.error('Ошибка при парсинге CSV файла:', error);
                    }
                });
            }
        });
    })();

    /* Запуск добавления сайта */
    $(document).on('click', '#startCFaddSite', (e) => {
        e.preventDefault();
        cloudflare('addSite');
    });

    /* Запуск включения Always Online */
    $(document).on('click', '#startCFAlwaysOnline', (e) => {
        e.preventDefault();
        cloudflare('alwaysOnline');
    });

    /* Запуск смены DNS */
    $(document).on('click', '#startCFchangeDNS', (e) => {
        e.preventDefault();
        cloudflare('changeDNS');
    });

    /* Запуск включения Always HTTPS */
    $(document).on('click', '#startCFAlwaysHttps', (e) => {
        e.preventDefault();
        cloudflare('alwaysHttps');
    });

    /* Запуск включения Full SSL */
    $(document).on('click', '#startCFFullSSL', (e) => {
        e.preventDefault();
        cloudflare('fullSSL');
    });

    /* Запуск включения Flexible SSL */
    $(document).on('click', '#startCFFlexibleSSL', (e) => {
        e.preventDefault();
        cloudflare('flexibleSSL');
    });

    /* Запуск отключения TLS 1.3 */
    $(document).on('click', '#disableTLS', (e) => {
        e.preventDefault();
        cloudflare('disableTLS');
    });

    /* Запуск очистки кеша */
    $(document).on('click', '#clearCache', (e) => {
        e.preventDefault();
        cloudflare('clearCache');
    });

    /* Запуск получения NS записей */
    let get_ns_request = false;
    $(document).on('click', '#startGetNS', (e) => {
        e.preventDefault();
        // не отправлять новый запрос, если не все сайты были обработаны
        if (get_ns_request) {
            return;
        }

        // список сайтов
        let sites = [];

        get_ns_request = true;

        cloudflare('getNS', 0, (site, response, last_item) => {
            let ns_records = response ? JSON.parse(response) : {};
            let ns1 = ns_records.ns1 !== undefined ? ns_records.ns1 : '';
            let ns2 = ns_records.ns2 !== undefined ? ns_records.ns2 : '';

            // данные для csv таблицы
            let site_data = [
                ns1,
                ns2,
                '',
                site.domain,
            ]
            // добавить в список обновлённые данные сайта
            sites.push(site_data);

            // если был обработан последний сайт в таблице
            if (last_item) {
                get_ns_request = false;

                // запрос на генерацию и загрузку csv файла с данными сайта и ns записями
                generateCSV(sites, ['NS1', 'NS2', 'DNS', 'Domain',]);
            }
        });
    });

    // data - массив данных
    // cols - массив с названиями столбцов (первый ряд в таблице)
    function generateCSV(data, cols) {
        $.ajax({
            type: 'POST',
            url: '/includes/generateCSV.php',
            data: {
                data: data,
                cols: cols
            },

            success: function (response) {
                let href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(response);
                $('body').append('<a class="nsrecords" href="' + href + '" download="nsrecords.csv"></a>')

                let link = $('.nsrecords')[0];
                link.click();
            },

            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Ошибка загрузки файла:', errorThrown);
            }
        });
    }

    /* WebArchive */
    (() => {
        let $form = $('.webarchive_form');
        if (!$form.length) return false;

        let $res = $('.table.result tbody');
        if (!$res.length) return false;

        $form.submit((e) => {
            e.preventDefault();

            $res.html();

            function parseURL(url) {
                let res = [];

                $.ajax({
                    type: "POST",
                    url: `/includes/webarchive.php`,
                    data: {url: url},
                    async: false,
                    // dataType: "applicat",
                    success: function (response) {
                        let $sitemaps = $(response).find('sitemap loc');
                        let $urls = $(response).find('url loc');

                        $sitemaps.each((index, elem) => {
                            let $this = $(elem);
                            let url = $this.html().replace('<!--[CDATA[', '').replace(']]-->', '');
                            let urls = parseURL(url);

                            urls.forEach(url => {
                                res.push(url);
                            });
                        });

                        $urls.each((index, elem) => {
                            let $this = $(elem);
                            let url = $this.html().replace('<!--[CDATA[', '').replace(']]-->', '');

                            res.push(url);
                        });
                    }
                });

                return res;
            }

            let $this = $(e.currentTarget);
            let url = $this.find('[name="sitemap_url"]').val();
            let res = [];
            let parse = parseURL(url, res);
            console.log('Ссылки успешно собраны', parse);

            $res.html($(`<tr><td><button class="btn btn-success w-100" id="startLoadingToWebArchive">Начать загрузку в WA</button></td></tr>`));
            parse.forEach(url => {
                $res.append($(`<tr><td>${url}</td></tr>`));
                // $.ajax({
                //     type: "POST",
                //     url: `/includes/webarchive.php`,
                //     data: {wa_url: url},
                //     async: false,
                //     success: function (response) {
                //         $res.append($(`<tr><td>${response}</td></tr>`));
                //         console.log(response);
                //     }
                // });
            });
        });

        $(document).on('click', '#startLoadingToWebArchive', (e) => {
            e.preventDefault();

            let $this = $(e.currentTarget);

            console.log($this);
        });
    })();

    /* ISP getDomains */
    $(document).on("click", "#startISPGetDomains", (e) => {
        e.preventDefault();

        let data = {};
        let $server = $('[name="server"]:checked');
        if (!$server.length) return false;

        data['server'] = $server.val();

        $.ajax({
            type: "POST",
            url: "/includes/isp.php?type=getDomains",
            data: data,
            success: function (response) {
                console.log(response);
            }
        });
    });

    /* ISP addDomains */
    $(document).on("click", "#startISPAddDomains", (e) => {
        e.preventDefault();

        let $this = $(e.currentTarget);
        let domains = $this.parent().find('[name="isp_domains"]').val().split("\n");
        let data = {};
        let $server = $('[name="server"]:checked');
        if (!$server.length) return false;

        data['server'] = $server.val();
        data['path'] = '';

        domains.forEach(domain => {
            data['domain'] = domain;

            $.ajax({
                type: "POST",
                url: "/includes/isp.php?type=addDomains",
                data: data,
                // async: false,
                success: function (response) {
                    console.log(response);
                }
            });
        });
    });

    /* Преобразование массива в многострочный текст */
    function arrayToMultilineHTML(array) {
        let text = '';

        $.each(array, function (index, value) {
            text += value + '<br>';
        });

        return text;
    }

    /* Indexing Sitemap */
    (() => {
        let $form = $('.indexing__form');
        if (!$form.length) return false;

        let $accordion = $('#indexing_accordion');
        let $accordion__item = $accordion.find('.accordion-item').clone();
        $accordion.find('.accordion-item').remove();

        $form.submit((e) => {
            e.preventDefault();

            let $this = $(e.currentTarget);
            let sitemaps = $this.find('#urls_list').val();

            $accordion.html('');

            if (!sitemaps.length) {
                alert('Заполните поле сайтмапов');
            }

            sitemaps = sitemaps.split('\n');
            sitemaps = sitemaps.filter(element => element !== '');

            function processArray(sitemaps, $accordion__item) {
                let currentIndex = 0;
                let step = 5;

                function processNextItem() {
                    let percent = currentIndex / sitemaps.length * 100;
                    percent = +percent.toFixed(2);
                    if (currentIndex < sitemaps.length) {
                        $('.ajax-loader').addClass('active');
                        $('.ajax_loader__text').text(percent + '%');
                        let sitemap = sitemaps[currentIndex];
                        $.ajax({
                            type: "POST",
                            url: `/includes/indexing.php`,
                            data: {sitemap: sitemap},
                            success: function (response) {
                                response = JSON.parse(response);
                                let id = 'sitemap__' + new Date().getTime();
                                let urls = arrayToMultilineHTML(response);
                                let $accordion__clone = $accordion__item.clone();

                                $accordion__clone.find('.accordion-button').text(sitemap);
                                $accordion__clone.find('.accordion-button').attr('data-bs-target', `#${id}`);
                                $accordion__clone.find('.accordion-collapse').attr('id', id);
                                $accordion__clone.find('.accordion-body').html(urls);
                                $accordion.append($accordion__clone);

                                if (!urls.length) {
                                    console.error(`${sitemap} - не удалось получить список URL`);
                                }
                            }
                        }).done((response) => {
                            currentIndex++;
                            processNextItem();
                        });
                    } else {
                        $('.ajax-loader').removeClass('active');
                        $('.ajax_loader__text').text('');
                        $('.btn_indexing_url').removeAttr('disabled');
                    }
                }

                processNextItem(); // Запускаем обработку первого элемента

            }

            processArray(sitemaps, $accordion__item);
        });
    })();

    /* Indexing URLs */
    (() => {
        let $btn = $('.btn_indexing_url');
        if (!$btn.length) return false;

        $(document).on('click', '.btn_indexing_url', (e) => {
            let $this = $(e.currentTarget);
            let typetask = $this.data('tasktype');

            if (!typetask.length) {
                typetask = 'google';
            }

            $('#indexing_accordion .accordion-item').each((index, elem) => {
                let $elem = $(elem);
                let $btn = $elem.find('.accordion-button');
                let urls = $elem.find('.accordion-body').html().split('<br>');
                let title = null;

                if ($btn.length) {
                    title = $btn.text().trim();
                }

                $elem.addClass('loading');

                urls = urls.filter(element => element.includes('http'));

                $.ajax({
                    type: "POST",
                    url: `/includes/indexing.php`,
                    data: {
                        urls: urls,
                        title: title,
                        typetask: typetask,
                    },
                    success: function (response) {
                        response = JSON.parse(response);

                        if (response.code == 0) {
                            $elem.removeClass('error');
                            $elem.addClass('success');
                        } else {
                            $elem.addClass('error');
                            $elem.removeClass('success');
                        }

                        $elem.removeClass('loading');
                    }
                });
            });
        });
    })();
});