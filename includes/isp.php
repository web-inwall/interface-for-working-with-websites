<?php
//header('Content-Type: text/plain');

require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/ISPAPI.php');

if (!isset($_GET['type'])) die('Тип запроса не определен');

$type = $_GET['type'];
$isp = new ISPAPI();

if ($type == 'getDomains') {
    if (empty($_POST['server'])) die('Наименование сервера не получено');
    print_r($isp->getDomains($_POST['server']));
}

if ($type == 'addDomains') {
//    print_r($_POST);die();
    if (empty($_POST['server'])) die('Наименование сервера не получено');
    if (empty($_POST['domain'])) die('Наименование домена не получено');
    if (empty($_POST['path'])) die('Директория не получена');

    print_r($isp->addDomain($_POST['server'], $_POST['domain'], $_POST['path']));
}