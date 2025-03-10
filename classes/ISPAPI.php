<?php
class ISPAPI
{
    protected $servers = [
        'primary' => [
            'ip' => '',
            'port' => '',
            'login' => '',
            'pass' => '',
        ],
        'secondary' => [
            'ip' => '',
            'port' => '',
            'login' => '',
            'pass' => '',
        ],
    ];

    protected function getCurrentServer($server_key)
    {
        $servers = $this->servers;

        return $servers[$server_key];
    }

    public function getDomains($server_key)
    {
        $server = $this->getCurrentServer($server_key);
        $apiUrl = 'https://' . $server['ip'] . ':' . $server['port'];

        // Данные для запроса всех доменов
        $data = array(
            'func' => 'domain',
            'out' => 'json', // Получить данные в формате JSON,
            'authinfo' => $server['login'] . ':' . $server['pass'] // Логин и пароль для авторизации
        );

        // Формируем HTTP-запрос
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));

        // Выполняем запрос к API
        $response = curl_exec($ch);

        // Проверяем ответ и обрабатываем его (например, декодируем JSON)
        if ($response === false) {
            return 'Ошибка запроса: ' . curl_error($ch);
        } else {
            $domains = json_decode($response, true); // Декодируем JSON в массив

            return $domains;
        }

        // Закрываем соединение
        curl_close($ch);
    }

    public function addDomain($server_key, $domain, $path)
    {
        $server = $this->getCurrentServer($server_key);
        $apiUrl = 'https://' . $server['ip'] . ':' . $server['port'];
        $data = array(
            'func' => 'site.edit',
            'elid' => '', // ID домена (может быть пустым для добавления нового домена)
            'sok' => 'ok',
            'site_name' => $domain,
            'site_aliases' => 'www.' . $domain,
            'site_home' => $path, // Директория для домена
        );

        // Формируем HTTP-запрос
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));

        // Выполняем запрос к API
        $response = curl_exec($ch);

        // Проверяем ответ и обрабатываем его (например, декодируем JSON)
        if ($response === false) {
            echo 'Ошибка запроса: ' . curl_error($ch);
        } else {
            return $response;
        }

        // Закрываем соединение
        curl_close($ch);
    }
}
