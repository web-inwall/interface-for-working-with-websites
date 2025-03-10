<?php
include('cloudflare.php');

foreach ($sites as $site) {
    if ($cf->clearCache($site['id'])) {
        echo $site['name'] . " -> " . "кеш очищен\n";
    }
}
die();
