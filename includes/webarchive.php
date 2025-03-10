<?php
//header('Content-Type: text/plain');

if (isset($_POST['url'])) {

    $url = $_POST['url'];
    $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36';
    $cookie_file = $_SERVER['DOCUMENT_ROOT'] . '/includes/cookie.txt';

    // Инициализация cURL сессии
    $ch = curl_init();

    // Устанавливаем URL и другие опции
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//    curl_setopt($ch, CURLOPT_HEADER, 1);

//    $headers = array(
//        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
//        'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7'
//    );
//    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Выполняем cURL запрос
    $response = curl_exec($ch);

    // Проверяем наличие ошибок
    if (curl_errno($ch)) {
        echo 'Ошибка cURL: ' . curl_error($ch);
    } else {
        // Выводим полученный контент
        echo $response;
    }

    // Закрываем cURL сессию
    curl_close($ch);
}

if (isset($_POST['wa_url'])) {
    $url = $_POST['wa_url'];

    // Инициализация cURL сессии
    $ch = curl_init();

    // Устанавливаем URL и другие опции
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Выполняем cURL запрос
    $response = curl_exec($ch);

    // Проверяем наличие ошибок
    if (curl_errno($ch)) {
        echo 'Ошибка cURL: ' . curl_error($ch);
    } else {
        // Выводим полученный контент
        echo $url . ' успешно отправлен в WebArchive';
    }

    // Закрываем cURL сессию
    curl_close($ch);
}
