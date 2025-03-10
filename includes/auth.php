<?php
// Запуск сессии
// session_start();

// Предположим, что у вас есть фиктивный логин и пароль
$users = USERS;

// Проверка, была ли отправлена форма входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Проверка логина и пароля
    if (isset($users[$username]) && $password === $users[$username]) {
        // Сохранение информации о пользователе в сессии
        $_SESSION['user'] = $username;

        // Перенаправление на главную страницу
        header('Location: /');
        exit;
    } else {
        header('Location: /?auth-error');
    }
}

// Выход пользователя
if (isset($_GET['logout'])) {
    // Очистка сессионных переменных
    session_unset();

    // Удаление сессии
    session_destroy();

    // Перенаправление на главную страницу
    header('Location: /');
    exit;
}
