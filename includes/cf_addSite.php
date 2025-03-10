<?php

include('cloudflare.php');
$result = false;
$result = $cf->createZone($_POST['domain']);
if ($result) echo 'success';
die();
