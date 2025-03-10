<?php
include('cloudflare.php');

foreach ($sites as $site) {
    if ($cf->fullSSL($site['id'])) {
        echo $site['name'] . " -> " . "Full SSL активирован\n";
    }
}
die();
