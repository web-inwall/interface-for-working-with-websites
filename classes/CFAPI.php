<?php
class CFAPI
{
    protected $is_auth = false;
    protected $api_key;
    protected $email;
    protected $account_id;

    protected function getAccountID($api_key, $email)
    {
        $url = 'https://api.cloudflare.com/client/v4/accounts';

        $headers = array(
            'X-Auth-Email: ' . $email,
            'X-Auth-Key: ' . $api_key
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);

        curl_close($ch);

        if ($responseData && isset($responseData['result'][0]['id'])) {
            $accountId = $responseData['result'][0]['id'];
            return $accountId;
        }

        return false;
    }

    protected function errorDie($msg)
    {
        file_put_contents('error.log', '[' . date('d.m.Y h:m:s') . '] ' . $msg . "\n", FILE_APPEND);
        die();
    }

    public function auth($api_key, $email, $account_id = null)
    {
        $this->api_key = $api_key;
        $this->email = $email;

        if (!empty($account_id) && empty($this->account_id)) {
            $this->account_id = $account_id;
        } elseif ($account_id = $this->getAccountID($api_key, $email)) {
            $this->account_id = $account_id;
        }

        $this->is_auth = true;

        return $this->is_auth;
    }

    protected function checkAuth()
    {
        if (!$this->is_auth) {
            $this->errorDie('auth error');
        }
    }

    public function createZone($domain)
    {
        $this->checkAuth();


        $url = 'https://api.cloudflare.com/client/v4/zones';

        $data = array(
            'account' => array('id' => $this->account_id),
            'name' => $domain,
            'jump_start' => true
        );

        $headers = array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key,
            'Content-Type: application/json'
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);

        curl_close($ch);

        if (!empty($responseData)) {
            return $responseData;
        } else {
            return $this->errorDie($response);
        }
    }

    public function getAllDNS($site_id)
    {
        $this->checkAuth();

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $site_id . '/dns_records';

        $headers = array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $responseData = json_decode($response, true);

        if ($responseData && isset($responseData['result'])) {
            return $responseData['result'];
        } else {
            $this->errorDie('Failed to get DNS records by function getAllDNS().');
        }

        curl_close($ch);
    }

    public function deleteDNS($site_id, $dns_id)
    {
        // Формируем URL эндпоинта для удаления DNS записи
        $url = 'https://api.cloudflare.com/client/v4/zones/' . $site_id . '/dns_records/' . $dns_id;

        // Настройки для cURL запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Выполняем запрос к Cloudflare API для удаления DNS записи
        $response = curl_exec($ch);
        curl_close($ch);

        // Обрабатываем результат
        if ($response) {
            $result = json_decode($response, true);

            if (isset($result['success']) && $result['success']) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->errorDie('Ошибка при выполнении запроса');
        }
    }

    public function addDNS($site_id, string $type, string $name, string $content, bool $proxied = true)
    {
        $this->checkAuth();

        $headers = array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key,
            'Content-Type: application/json'
        );

        // Step 1: Удаляем все существующие DNS-записи
        $urlDelete = 'https://api.cloudflare.com/client/v4/zones/' . $site_id . '/purge_cache';
        $dataDelete = array('purge_everything' => true);

        $chDelete = curl_init($urlDelete);
        curl_setopt($chDelete, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chDelete, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($chDelete, CURLOPT_POSTFIELDS, json_encode($dataDelete));
        curl_setopt($chDelete, CURLOPT_HTTPHEADER, $headers);

        $responseDelete = curl_exec($chDelete);

        // Step 2: Добавляем новую DNS-запись типа A
        $urlAdd = 'https://api.cloudflare.com/client/v4/zones/' . $site_id . '/dns_records';
        $dataAdd = array(
            'type' => $type,
            'name' => $name,
            'content' => $content,
            'proxied' => $proxied,
        );

        $chAdd = curl_init($urlAdd);
        curl_setopt($chAdd, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chAdd, CURLOPT_POST, true);
        curl_setopt($chAdd, CURLOPT_POSTFIELDS, json_encode($dataAdd));
        curl_setopt($chAdd, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($chAdd);
        $responseAdd = json_decode($result, true);

        return $responseAdd;
        if (isset($responseAdd['success'])) return $responseAdd['success'];
        else return false;
    }

    public function getAllSites()
    {
        $this->checkAuth();

        // Устанавливаем URL для запроса к API CloudFlare
        $url = 'https://api.cloudflare.com/client/v4/zones';

        // Настройки для cURL запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Выполняем запрос к API CloudFlare
        $response = curl_exec($ch);
        curl_close($ch);

        // Проверяем и обрабатываем результат
        if ($response) {
            $zones = json_decode($response, true);

            if (isset($zones['result'])) {
                return $zones['result'];
            } else {
                $this->errorDie('Ошибка при получении списка зон: ' . $zones['errors'][0]['message']);
            }
        } else {
            $this->errorDie('Ошибка при выполнении запроса');
        }
    }

    public function alwaysOnline($site_id, bool $is_on = true)
    {
        $this->checkAuth();

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $site_id . '/settings/always_online';

        $data = array(
            'value' => 'on',
            'resolution' => 'online'
        );

        // Настройки для cURL запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Выполняем запрос к API CloudFlare
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            return $result['success'];
        } else {
            $this->errorDie('Ошибка при выполнении запроса');
        }
    }

    public function alwaysHttps($site_id, bool $is_on = true)
    {
        $this->checkAuth();

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $site_id . '/settings/always_use_https';

        $data = array(
            'value' => 'on',
            'resolution' => 'https'
        );

        // Настройки для cURL запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Выполняем запрос к API CloudFlare
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            return $result['success'];
        } else {
            $this->errorDie('Ошибка при выполнении запроса');
        }
    }

    public function fullSSL($site_id, bool $is_on = true)
    {
        $this->checkAuth();

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $site_id . '/settings/ssl';

        $data = array(
            'value' => 'full',
        );

        // Настройки для cURL запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Выполняем запрос к API CloudFlare
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            return $result['success'];
        } else {
            $this->errorDie('Ошибка при выполнении запроса');
        }
    }

    public function flexibleSSL($site_id, bool $is_on = true)
    {
        $this->checkAuth();

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $site_id . '/settings/ssl';

        $data = array(
            'value' => 'flexible',
        );

        // Настройки для cURL запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Выполняем запрос к API CloudFlare
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            return $result['success'];
        } else {
            $this->errorDie('Ошибка при выполнении запроса');
        }
    }

    public function disableTLS($site_id, bool $is_on = true)
    {
        $this->checkAuth();

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $site_id . '/settings/tls_1_3';

        $data = array(
            'value' => 'off',
        );

        // Настройки для cURL запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Выполняем запрос к API CloudFlare
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            return $result['success'];
        } else {
            $this->errorDie('Ошибка при выполнении запроса');
        }
    }

    public function clearCache($site_id, bool $is_on = true)
    {
        $this->checkAuth();

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $site_id . '/purge_cache';

        $data = array(
            'purge_everything' => true,
        );

        // Настройки для cURL запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Выполняем запрос к API CloudFlare
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            return $result['success'];
        } else {
            $this->errorDie('Ошибка при выполнении запроса');
        }
    }

    public function getNS($site_id)
    {

        $this->checkAuth();

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $site_id;

        // Настройки для cURL запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: ' . $this->email,
            'X-Auth-Key: ' . $this->api_key,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            $ns = isset($result['result']['name_servers']) && is_array($result['result']['name_servers']) ? $result['result']['name_servers'] : [];
            $records = [
                'ns1' => !empty($ns[0]) ? $ns[0] : '',
                'ns2' => !empty($ns[1]) ? $ns[1] : ''
            ];

            return $records;
        } else {
            $this->errorDie('Ошибка при выполнении запроса');
        }
    }
}
