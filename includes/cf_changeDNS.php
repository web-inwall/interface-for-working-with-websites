<?php
include('cloudflare.php');
$result = false;
foreach ($sites as $site) {
    $all_dns = $cf->getAllDNS($site['id']);
    if (!empty($all_dns)) {
        foreach ($all_dns as $dns) {
            $cf->deleteDNS($dns['zone_id'], $dns['id']);
        }
    }
    
    $result = $cf->addDNS($site['id'], 'A', '@', $ip, true);
}
if ($result) echo 'success';
die();
