<?php
include('cloudflare.php');

foreach ($sites as $site) {
    if ($cf->alwaysOnline($site['id'])) {
        echo $site['name'] . " -> " . "Always Online активирован\n";
    }
}
die();
