<?php
$limit = 10;

if (isset($_GET['site'])) {
    $site = trim($_GET['site']);

    $json = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $site . '.json');

    echo  $counter = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $site . '.tmp');
    echo "<br>";

    $ex = explode("/", $counter);
    $current = $ex[1];

    if ($ex[0] >= $ex[1]) {
        if ($json === false) {
            die('Error reading the JSON file');
        }

        $json_data = json_decode($json, true);

        if ($json_data === null) {
            die('Error decoding the JSON file');
        }



        for ($i = $current; $i <= ($current + $limit) - 1; $i++) {
            echo $i;
            echo $item = trim($json_data['data'][$i]);
            echo "-";

            $url = $item;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
            curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $output = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            echo 'HTTP code: ' . $httpcode . '<br>';

            if ($httpcode == '200') {
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $site . '-ok.txt', $i . " - " . $item . ' - ' . $httpcode . "\n", FILE_APPEND);
            } else {
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $site . '-errors.txt', $i . " - " . $item . ' - ' . $httpcode . "\n", FILE_APPEND);
            }
        }

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tmp/' . $site . '.tmp', $ex[0] . "/" . $i);
    } else {
        return false;
    }
}
