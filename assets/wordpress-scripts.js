jQuery(document).ready(($) => {
	// AJAX
		class AJAXRequest {
			constructor(url, data = {}, type = 'GET', options = {}) {
				this.url = url;
				this.type = type;
				this.data = data;
				this.options = options;
			}

			request() {
				let ajaxOptions = {
					type: this.type,
					url: this.type == 'GET' ? this.url + '?' + new URLSearchParams(this.data).toString() : this.url ,
					data: this.data
				};

				let keys = Object.keys(this.options);
				for ( let i = 0; i < keys.length; i++ ) {
					ajaxOptions[keys[i]] = this.options[keys[i]];
				}

				$.ajax(ajaxOptions);
			}
		}

	// Sites Table
		class SitesTable {
			constructor(fields) {
				this.fields = {
					id: 'ID'
				};

				this.data = [];

				this.mapFields(fields);
			}

			mapFields(fields) {
				let keys = Object.keys(fields);
				for ( let i = 0; i < keys.length; i++ ) {
					this.fields[keys[i]] = fields[keys[i]];
				}
			}

			resetData() {
				this.data = [];
			}

			addDataItem(data) {
				let newItem = {};
				let headerKeys = Object.keys(this.fields);
				for ( let i = 0; i < headerKeys.length; i++ ) {
					let key = headerKeys[i];
					newItem[key] = data[key] !== undefined ? data[key] : '';
				}

				newItem.id = this.data.length;

				this.data.push(newItem);
			}

			addDataItemFromArr(dataArr) {
				let newItem = {};
				newItem.id = this.data.length;
				let headerKeys = Object.keys(this.fields);
				// отступ, потому что первый ключ это id
				headerKeys.shift();
				for ( let i = 0; i < headerKeys.length; i++ ) {
					let key = headerKeys[i];
					newItem[key] = dataArr[i] !== undefined ? dataArr[i] : '';
				}

				this.data.push(newItem);
			}

			addField(key, label, defaultValue = '') {
				this.fields[key] = label;

				for ( let i = 0; i < this.data.length; i++ ) {
					this.data[i][key] = defaultValue;
				}
			}

			getFields() {
				return this.fields;
			}

			updateDataByID(id, key, value) {
				this.data[id][key] = value;
			}

			getData() {
				return this.data;
			}

			getDataByID(id) {
				return this.data[id];
			}

			getNextById(id) {
				return this.data[id + 1] !== undefined ? this.data[id + 1] : false;
			}

			getFirst() {
				return this.data[0] !== undefined ? this.data[0] : false;
			}
		}

		class SitesTableView {
			constructor(wrapper) {
				this.wrapper = wrapper;
				this.templates = {
					table: `<div class="tool-sites-table-wrapper">
						<table class="tool-sites-table">
							<thead class="tool-sites-table-header"></thead>
							<tbody class="tool-sites-table-body"></tbody>
						</table>

						<div class="tool-sites-table-controls">
							<div class="tool-sites-table-control tool-sites-table-upload-csv">
								<label>
									<span class="btn">Импорт из csv файла</span>
									<input type="file">
								</label>
							</div>

							<div class="tool-sites-table-control tool-sites-table-clear">
								<button type="button" class="btn">Очистить таблицу</button>
							</div>
						</div>
					</div>`,

					tableRow: '<tr class="tool-sites-table-row"></tr>',

					tableCol: '<td class="tool-sites-table-col"></td>'
				}
			}

			renderTable(fields, data) {
				let table = $(this.templates.table);

				this.renderHeader(table, fields);
				for ( let i = 0; i < data.length; i++ ) {
					this.renderRow(table, data[i]);
				}
				
				this.wrapper.html(table);
				this.hide('.tool-sites-table-wrapper');
				this.show('.tool-sites-table-wrapper', 500);

				if ( this.find('.tool-sites-table-body .tool-sites-table-row').length ) {
					this.show('.tool-sites-table-clear');
					this.hide('.tool-sites-table-upload-csv');
				} else {
					this.hide('.tool-sites-table-clear');
					this.show('.tool-sites-table-upload-csv');
				}
			}

			renderHeader(table, fields) {
				let headerRow = $(this.templates.tableRow);
				let headerKeys = Object.keys(fields);

				for ( let i = 0; i < headerKeys.length; i++ ) {
					this.renderCol(headerRow, headerKeys[i], fields[headerKeys[i]]);
				}

				table.find('.tool-sites-table-header').append(headerRow);
			}

			renderRow(table, data) {
				let bodyRow = $(this.templates.tableRow);
				bodyRow.attr('data-id', data.id);

				let keys = Object.keys(data);
				for ( let i = 0; i < keys.length; i++ ) {
					this.renderCol(bodyRow, keys[i], data[keys[i]]);
				}

				table.find('.tool-sites-table-body').append(bodyRow);
			}

			renderCol(row, key, value) {
				let bodyCol = $(this.templates.tableCol);
				bodyCol.attr('data-field', key);
				bodyCol.text(value);
				row.append(bodyCol);
			}

			updateRow(id, data) {
				let bodyRow = this.find('.tool-sites-table-body .tool-sites-table-row[data-id="' + id + '"]');
				bodyRow.html('');

				let keys = Object.keys(data);
				for ( let i = 0; i < keys.length; i++ ) {
					this.renderCol(bodyRow, keys[i], data[keys[i]]);
				}

				this.hide(bodyRow);
				this.show(bodyRow, 500);
			}

			highlightRow(id, color) {
				this.find('.tool-sites-table-body .tool-sites-table-row[data-id="' + id + '"]').css('background-color', color);
			}

			lockControls() {
				this.find('button').attr('disabled', 'disabled');
			}

			unlockControls() {
				this.find('button').removeAttr('disabled');
			}

			show(item, transition = 0) {
				if ( typeof(item) === 'string' ) {
					this.find(item).fadeIn(transition);
				} else {
					item.fadeIn(transition);
				}
			}

			hide(item, transition = 0) {
				if ( typeof(item) === 'string' ) {
					this.find(item).fadeOut(transition);
				} else {
					item.fadeOut(transition);
				}
			}

			find(itemClass) {
				return this.wrapper.find(itemClass);
			}
		}

		class SitesTableController {
			constructor(sitesTable, sitesTableView) {
				this.sitesTable = sitesTable;
				this.sitesTableView = sitesTableView;
			}

			init() {
				this.sitesTableView.renderTable(this.sitesTable.getFields(), this.sitesTable.getData());
				this.registerEvents();
			}

			reload() {
				this.sitesTableView.renderTable(this.sitesTable.getFields(), this.sitesTable.getData());
				this.registerEvents();
			}

			reloadRow(id) {
				this.sitesTableView.updateRow(id, this.sitesTable.getDataByID(id));
			}

			parseFile(file) {
				if ( file.type !== 'text/csv' ) {
					console.log('Выбранный файл не csv таблица');
					return;
				}

				Papa.parse(file, {
	        header: false,
	        complete: (results) => {
	        	for ( let i = 0; i < results.data.length; i++ ) {
	        		this.sitesTable.addDataItemFromArr(results.data[i]);
	        	}

						this.sitesTableView.renderTable(this.sitesTable.getFields(), this.sitesTable.getData());
						this.registerEvents();
	        },

	        error: (error) => {
	          console.log('Ошибка при парсинге CSV файла:', error);
	        }
	      });
			}

			registerEvents() {
				this.sitesTableView.find('.tool-sites-table-upload-csv').on('change', (e) => {
					let item = $(e.currentTarget);
					let file = e.target.files[0];

					if ( !file ) {
						return;
					}

					this.parseFile(file);
				});

				this.sitesTableView.find('.tool-sites-table-clear button').on('click', (e) => {
					this.sitesTable.resetData();
					this.sitesTableView.renderTable(this.sitesTable.getFields(), this.sitesTable.getData());
					this.registerEvents();
				});
			}
		}

	// Tools
		class Tool {
			constructor(sitesTable, sitesTableController, sitesTableView) {
				this.sitesTable = sitesTable;
				this.sitesTableController = sitesTableController;
				this.sitesTableView = sitesTableView;

				this.toolRequest = false;
				this.requestLabel = 'Загрузка...';
				this.ajaxURL = '';

				this.loaderColor = '#ddf9ff';
				this.successColor = '#d1efd1';
				this.errorColor = '#ffd6d6';

				this.formData = new FormData();
			}

			init() {
				this.sitesTable.addField('status', 'Состояние');
				this.sitesTableController.reload();
			}

			addToFormData(key, value) {
				this.formData.append(key, value);
			}

			request(options, callback = function() {}) {
				if ( !this.ajaxURL ) {
					console.log('Укажите url для запроса');
					callback();
					return;
				}

				if ( this.toolRequest ) {
					console.log('Дождитесь завершения текущей установки');
					callback();
					return;
				}

				let firstItem = this.sitesTable.getFirst();
				if ( !firstItem ) {
					console.log('Нет сайтов в таблице');
					callback();
					return;
				}

				this.toolRequest = true;
				this.sitesTableView.lockControls();

				// типа приватный метод для запроса
				let request = (site) => {
					new Promise((resolve, reject) => {
						this.sitesTable.updateDataByID(site.id, 'status', this.requestLabel);
						this.sitesTableController.reloadRow(site.id);
						this.sitesTableView.highlightRow(site.id, this.loaderColor);

						let keys = Object.keys(site);
						for ( let n = 0; n < keys.length; n++ ) {
							this.addToFormData(keys[n], site[keys[n]]);
						}

						options.success = (results) => {
							let data = {};
							let message = '';
							let color = '';

							try {
								data = JSON.parse(results);
								message = data.message;
								let error = data.error;
								color = !error ? this.successColor : this.errorColor;
							} catch (errorMessage) {
								message = errorMessage;
								color = this.errorColor
							}
							
							this.sitesTable.updateDataByID(site.id, 'status', message);
							this.sitesTableView.highlightRow(site.id, color);
						};

						options.error = (error) => {
							this.sitesTable.updateDataByID(site.id, 'status', 'Ошибка во время ajax запроса');
							console.log('Ошибка во время ajax запроса', error);
							this.sitesTableView.highlightRow(site.id, this.errorColor);
							reject(error);
						};

						options.complete = () => {
							this.sitesTableController.reloadRow(site.id);
							resolve(site);
						};

						let ajaxRequest = new AJAXRequest(this.ajaxURL, this.formData, 'POST', options);
						ajaxRequest.request();
					}).then((site) => {
						let nextSite = this.sitesTable.getNextById(site.id);
						if ( nextSite ) {
							request(nextSite);
						} else {
							this.toolRequest = false;
							this.sitesTableView.unlockControls();
							callback();
						}
					}).catch((error) => {
						this.sitesTable.updateDataByID(site.id, 'status', 'Ошибка');
						this.sitesTableView.highlightRow(site.id, this.errorColor);
						this.sitesTableController.reloadRow(site.id);
						console.log('Ошибка во время работы инструмента', error);

						let nextSite = this.sitesTable.getNextById(site.id);
						if ( nextSite ) {
							request(nextSite);
						} else {
							this.toolRequest = false;
							this.sitesTableView.unlockControls();
							callback();
						}
					});
				};


				request(firstItem);
			}
		}

		class WPPluginsInstaller extends Tool {
			constructor(sitesTable, sitesTableController, sitesTableView) {
				super(sitesTable, sitesTableController, sitesTableView);

				this.ajaxURL = '/includes/wp_installPlugins.php';
				this.requestLabel = 'Установка...';
			}
		}

	// таблица с сайтами
		let wrapper = $('.tool-tab-content-wrapper[data-id="install-plugins"] .tool-sites-table-main-wrapper');
		let sitesTable = new SitesTable({domain_path: 'Домен', host: 'Хост', port: 'Порт', login: 'Логин', password: 'Пароль'});
		let sitesTableView = new SitesTableView(wrapper);
		let sitesTableController = new SitesTableController(sitesTable, sitesTableView);
		let pluginsInstaller = new WPPluginsInstaller(sitesTable, sitesTableController, sitesTableView);

		sitesTableController.init();
		pluginsInstaller.init();

	// запуск обработки сайтов в таблице
		$('#startWPInstallPlugins').on('click', function() {
			$(this).attr('disabled', 'disabled');
			let archive = $('.upload-file').find('input')[0].files[0];
			pluginsInstaller.addToFormData('plugins', archive);
			pluginsInstaller.request({
				processData: false,
				contentType: false,
			}, () => {
				$(this).removeAttr('disabled');
				console.log('Обработка всех сайтов в таблице завершена');
			});
		});

	// вкладки
		$('.tool-tabs-list-item').click( function() {
			let current = $(this);
			let id = current.attr('data-id');
			let active = $('.tool-tabs-list').find('.tool-tabs-list-item-active');
			let active_content = $('.tool-tabs-content-wrapper').find('.tool-tab-content-active');

			active.removeClass('tool-tabs-list-item-active');
			active_content.removeClass('tool-tab-content-active');
			current.addClass('tool-tabs-list-item-active');

			$('.tool-tabs-content-wrapper').find('.tool-tab-content-wrapper').hide();
			$('.tool-tabs-content-wrapper').find('.tool-tab-content-wrapper[data-id="' + id + '"]').fadeIn(500).addClass('tool-tab-content-active');
		});
});