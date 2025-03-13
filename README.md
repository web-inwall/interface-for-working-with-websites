# interface-for-working-with-websites

Интерфейс для работы с веб-сайтами, автоматизирующий взаимодействие с различными сервисами, такими как Bukvarix, CloudFlare, WebArchive, ISP Manager и WordPress, SiteInspector.

## Описание

Этот проект представляет собой набор PHP-классов и скриптов, предназначенных для автоматизации рутинных задач, связанных с управлением веб-сайтами. Он позволяет упростить и ускорить процессы, такие как проверка доменов, управление DNS-записями, создание резервных копий в WebArchive, управление доменами на ISP Manager и установку плагинов WordPress.

## Функциональность

- **Bukvarix API**: Проверка доменов и получение информации о ключевых словах. Кэширование результатов для оптимизации производительности. Экспорт данных в формат XLSX.

- **ISPAPI**: Получение списка доменов с серверов ISP Manager. Добавление новых доменов на сервер.

- **SiteInspector**: Проверка доступности URL-адресов и получение HTTP-кодов ответов. 

- **CloudFlare API (CFAPI)**: Авторизация в CloudFlare. Получение списка сайтов. Создание новых зон (сайтов). Управление DNS-записями (добавление, удаление). Управление настройками безопасности и производительности (Always Online, Always HTTPS, SSL, TLS). Очистка кэша.

- **WebArchive**: Отправка URL-адресов в WebArchive для создания резервных копий.

- **WordPress**: Автоматическая установка плагинов WordPress через SFTP.

## Демонстрация
![Screenshot_2](https://github.com/user-attachments/assets/76f51dbd-267b-46ed-b44b-50da34c7cd85)

![Screenshot_1](https://github.com/user-attachments/assets/6f7f473e-501c-4a9f-b379-e83113949b31)

![Screenshot_3](https://github.com/user-attachments/assets/5b58e177-688d-4177-bb36-d9fd6a0aed54)

## Установка и запуск

1. Клонируйте репозиторий interface-for-working-with-websites на свой сервер.
2. Установите необходимые зависимости, используя Composer: composer install
3. Настройте конфигурационные файлы:
- Укажите параметры доступа к API Bukvarix (API key).
- Настройте параметры подключения к серверам ISP Manager (IP-адрес, порт, логин, пароль).
- Укажите данные для доступа к API CloudFlare (email, API key, account ID).
- При необходимости, настройте параметры аутентификации пользователей (логины и пароли).
4. Настройте веб-сервер для обработки PHP-скриптов.
5. Установите права на запись для директории cache/, используемой для кэширования данных Bukvarix.

## Требования
- PHP 7.4 или выше
- Расширения PHP: curl, json, openssl, mbstring, zip (для работы с архивами)
- Composer
- Библиотека PhpSpreadsheet для работы с XLSX файлами (phpoffice/phpspreadsheet)
- Библиотека phpseclib3 для SFTP соединения (phpseclib/phpseclib)

### Контактная информация

https://github.com/web-inwall/
https://t.me/inwall_ch
