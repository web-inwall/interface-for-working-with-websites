<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

    <form class="indexing__form">
        <div class="row mb-3">
            <div class="col-4">
                <button type="submit" class="btn btn-success w-100">Считать сайтмапы</button>
            </div>
            <div class="col-4">
                <button type="button" class="btn btn-success w-100 btn_indexing_url" disabled data-tasktype="google">
                    Проиндексировать в Google
                </button>
            </div>
            <div class="col-4">
                <button type="button" class="btn btn-success w-100 btn_indexing_url" disabled data-tasktype="yandex">
                    Проиндексировать в Yandex
                </button>
            </div>
        </div>
        <div class="mb-3">
            <label for="urls_list" class="form-label">Введите список URL на сайтмапы</label>
            <textarea class="form-control" id="urls_list" rows="5" placeholder="https://domain.com/sitemap.xml"></textarea>
        </div>
    </form>

    <div class="accordion" id="indexing_accordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#sitemap__1" aria-expanded="false" aria-controls="collapseOne"></button>
            </h2>
            <div id="sitemap__1" class="accordion-collapse collapse" aria-labelledby="headingOne"
                 data-bs-parent="#indexing_accordion">
                <div class="accordion-body"></div>
            </div>
        </div>
    </div>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>