<?php
$Bukvarix = new BukvarixAPI();

if (isset($_POST['domain_list'])) {
    $domain_list = $_POST['domain_list'];
    $is_visible_keys = isset($_POST['visible_keys']);
    $domains = preg_split('/\r\n|\r|\n/', $domain_list);
    $result = $Bukvarix->checkDomains($domains, $is_visible_keys);
}

include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php');

$domain_list = null;
if (isset($_POST['domain_list'])) {
    $domain_list = $_POST['domain_list'];
}
?>

<h1>Проверить домены в Букварикс</h1>

<form method="POST" action="/">
    <div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">Введите домены, которые хотите проверить в Буквариксе. Каждый домен должен находится на отдельной строке</label>
        <textarea class="form-control" name="domain_list" rows="5" required placeholder="domain1.com&#10;domain2.com&#10;..."><?= (!empty($domain_list)) ? trim($domain_list) : '' ?></textarea>
    </div>
    <div class="row mt-3 align-items-center">
        <div class="col-auto me-auto">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" name="visible_keys" <?= (!empty($_POST['visible_keys'])) ? 'checked="checked"' : '' ?>>
                <label class="form-check-label" for="flexSwitchCheckChecked">Включить отображение ключей</label>
            </div>
        </div>
        <div class="col-2">
            <button type="submit" class="btn btn-primary w-100">Проверить</button>
        </div>
        <div class="col-2">
            <a href="/last_output.xlsx" class="btn btn-success w-100">Скачать</a>
        </div>
        <div class="col-2">
            <a href="/" class="btn btn-danger w-100">Сбросить</a>
        </div>
    </div>
</form>

<hr>

<?php if (!empty($result) && is_array($result)) { ?>
    <div class="accordion" id="accordionExample">
        <?php $count = 0; ?>
        <?php foreach ($result as $domain => $res) { ?>
            <?php $count++; ?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $count ?>" aria-expanded="false" aria-controls="collapse<?= $count ?>">
                        <?= $domain ?>
                        <?php if (!empty($res['count_keys'])) { ?>
                            (<?= $res['count_keys'] ?>)
                        <?php } ?>
                    </button>
                </h2>

                <?php if (is_array($res['keys'])) { ?>
                    <div id="collapse<?= $count ?>" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <ul>
                                <?php foreach ($res['keys'] as $key) { ?>
                                    <li><?= $key ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php') ?>