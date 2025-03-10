<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

function messageLog($msg) {
    @file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/indexing.log', '[' . date("d.m.Y h:i:s", time()) . '] - ' . $msg . "\n", FILE_APPEND);
}

function readingSitemap($sitemap, &$urls = [])
{
    if (stripos($sitemap, "http") === false) {
        $sitemap = "https://" . $sitemap;
    }

    $last_sym = substr($sitemap, -1);
    if ($last_sym !== "/") {
        $sitemap .= "/";
    }

    // Инициализация cURL сессии
    $ch = curl_init();

    // Устанавливаем URL и другие опции
    curl_setopt($ch, CURLOPT_URL, $sitemap);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, -1);

    // Выполняем cURL запрос
    $response = curl_exec($ch);

    // Проверяем наличие ошибок

    if (curl_errno($ch)) {
        messageLog('Ошибка cURL (' . $sitemap . '): ' . curl_error($ch));
    } else {
        // Выводим полученный контент
        preg_match_all('/\<loc\>(.*?)\<\/loc\>/s', $response, $matches);

        foreach ($matches[1] as $loc) {
            $loc = str_replace('<![CDATA[', '', $loc);
            $loc = str_replace(']]>', '', $loc);
            if (strpos($loc, '.xml') !== false) {
                readingSitemap($loc, $urls);
            } else {
                $urls[] = $loc;
            }
        }
    }

    // Закрываем cURL сессию
    curl_close($ch);
}

if (isset($_POST['sitemap'])) {
    $sitemap = $_POST['sitemap'];

    $urls = [];
    readingSitemap($sitemap, $urls);

    if (empty($urls)) {
        $sitemap = str_replace('/sitemap.xml', '/sitemap_index.xml', $sitemap);
        readingSitemap($sitemap, $urls);
    }

    echo json_encode($urls, 320);
}

if (isset($_POST['urls'])) {
    $urls = $_POST['urls'];

    if (isset($_POST['typetask']) && !empty($_POST['typetask'])) {
        $typetask = $_POST['typetask'];
    } else {
        $typetask = 'google';
    }

    $api_url = 'https://api.speedyindex.com/v2/task/'.$typetask.'/indexer/create';
    $fields = [
        'urls' => $urls,
    ];

    if (isset($_POST['title']) && !empty($_POST['title'])) {
        $fields['title'] = $_POST['title'];
    }

    // Инициализация cURL сессии
    $ch = curl_init();

    // Устанавливаем URL и другие опции
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . SPEEDY_INDEX_API_KEY,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    // Выполняем cURL запрос
    $response = curl_exec($ch);

    // Проверяем наличие ошибок
    if (curl_errno($ch)) {
        echo 'Ошибка cURL: ' . curl_error($ch);
    } else {
        echo $response;
    }
}