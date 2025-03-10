<?php

// Запуск сессии
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/BukvarixAPI.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/CFAPI.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ISPAPI.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/result_tmp.php';

// Проверяем, авторизован ли пользователь
if (isset($_GET['auth-error'])) {
    include $_SERVER['DOCUMENT_ROOT'] . '/templates/auth-error.php';
} else if (isset($_SESSION['user'])) {
    // Получаем URL запроса
    $request_uri = $_SERVER['REQUEST_URI'];

    // Определяем маршруты
    $routes = array(
        '/' => 'bukvarix',
        '/index.php' => 'bukvarix',
        '/bukvarix' => 'bukvarix',
        '/cloudflare' => 'cloudflare',
        '/indexing' => 'indexing',
        '/webarchive' => 'webarchive',
        '/isp' => 'isp',
        '/wordpress' => 'wordpress',
        '/siteinspector' => 'siteinspector',
        // Добавьте другие маршруты по мере необходимости
    );

    // Поиск соответствующего обработчика для URL
    $view = null;
    foreach ($routes as $route => $viewName) {
        //         var_dump($request_uri, $route);die();
        if ($request_uri === $route) {
            $view = $viewName;
            break;
        }
    }

    // Вызов соответствующего обработчика
    if ($view) {
        include_once $_SERVER['DOCUMENT_ROOT'] . '/pages/' . $view . '.php'; // Например, подключение шаблона для вывода страницы
    } else {
        echo '404 Not Found'; // Страница не найдена
    }
} else {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/templates/login.php';
}
