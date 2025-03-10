<?php
include('cloudflare.php');

foreach ($sites as $site) {
    if ($cf->AlwaysHttps($site['id'])) {
        echo $site['name'] . " -> " . "Always Https активирован\n";
    }
}
die();
