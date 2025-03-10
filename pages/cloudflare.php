<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

    <div class="row mb-3">
        <div class="col-4">
            <button class="btn btn-success w-100" id="startImportCSV">Импорт CSV</button>
            <input class="d-none" type="file" name="import-csv-file">
        </div>
        <div class="col-4">
            <button class="btn btn-success w-100" id="startCFaddSite">Запустить добавление сайта в CF</button>
        </div>
        <div class="col-4">
            <button class="btn btn-success w-100" id="startCFchangeDNS">Запустить смену DNS</button>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-4">
            <button class="btn btn-success w-100" id="startCFAlwaysOnline">Запустить включение Always Online</button>
        </div>
        <div class="col-4">
            <button class="btn btn-success w-100" id="startCFAlwaysHttps">Запустить Always HTTPS</button>
        </div>
        <div class="col-4">
            <button class="btn btn-success w-100" id="startCFFullSSL">Запустить Full SSL</button>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-4">
            <button class="btn btn-success w-100" id="startCFFlexibleSSL">Запустить Flexible SSL</button>
        </div>
        <div class="col-4">
            <button class="btn btn-success w-100" id="startGetNS">Запустить получение NS записей</button>
        </div>
        <div class="col-4">
            <button class="btn btn-success w-100" id="disableTLS">Отключить TLS 1.3</button>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <button class="btn btn-success w-100" id="clearCache">Очистить кеш</button>
        </div>
    </div>

<?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/tmp/import.csv')) { ?>
    <?php
    $file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/tmp/import.csv');
    $accounts = str_getcsv($file, PHP_EOL);
    ?>
    <div style="max-height: 50vh; overflow: auto">
        <table class="table align-middle sites__data">
            <thead>
            <tr>
                <th scope="col">Domain</th>
                <th scope="col">E-mail</th>
                <th scope="col">Password</th>
                <th scope="col">API key</th>
                <th scope="col">DNS IP</th>
                <th scope="col">Result</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
<?php } ?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>