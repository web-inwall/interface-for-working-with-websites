<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

<?php

if (isset($_POST['sites'])) $data = trim($_POST['sites']);
if (isset($_POST['tag'])) $tag = trim($_POST['tag']);
if (!empty($data) && !empty($tag)) {

    $array = explode("\n", $data);
    $array2 = array('data' => $array);
    $json_data = json_encode($array2);
    $counter = count($array) . "/0";

    //основной файл со списком URL 
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $tag . '.json', $json_data);

    //счетчик 
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $tag . '.tmp', $counter);

    //для ошибок 
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $tag . '-errors.txt', '');
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $tag . '-ok.txt', '');
}

?>
<form method="POST" class="siteinspector__form">
    <div class="mb-3">
        <input class="form-control" name="tag" placeholder="название на латинице"></input>
    </div>
    <div class="mb-3">
        <label for="urls_list" class="form-label">Введите список URL</label>
        <textarea class="form-control" id="urls_list" rows="5" placeholder="https://domain.com/" name="sites"></textarea>
    </div>
    <input type="submit" class="btn btn-primary w-100">
</form>




<?php include($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>