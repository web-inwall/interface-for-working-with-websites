<?php
include('cloudflare.php');

foreach ($sites as $site) {
    if ($cf->disableTLS($site['id'])) {
        echo $site['name'] . " -> " . "Flexible SSL активирован\n";
    }
}
die();
