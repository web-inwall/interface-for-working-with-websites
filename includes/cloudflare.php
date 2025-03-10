<?php
header('Content-Type: text/plain');

/* Ajax Test */
//sleep(2);die('test success');

require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/CFAPI.php');

if (!isset($_POST) || empty($_POST)) die('empty POST');

$cf = new CFAPI();

$domain = $_POST['domain'];
$email = $_POST['email'];
$pass = $_POST['pass'];
$api_key = $_POST['api'];
$ip = $_POST['ip'];

$cf->auth($api_key, $email);
$sites = $cf->getAllSites();