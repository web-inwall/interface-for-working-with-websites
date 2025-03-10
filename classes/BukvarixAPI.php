<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BukvarixAPI
{
    protected $api_key = '';
    protected $url = 'http://api.bukvarix.com/v1/site/';

    public function checkDomain($domain, $is_visible_keys = false)
    {
        $domain = urlencode($domain);
        $cache_dir = $_SERVER['DOCUMENT_ROOT'] . '/cache/';
        $cache_filepath = $cache_dir . md5($domain) . '.txt';

        /* Кэширование */
        if (file_exists($cache_filepath)) {
            $creation_timestamp = filectime($cache_filepath);
            $current_timestamp = time();
            $age_in_seconds = $current_timestamp - $creation_timestamp;
            $age_in_days = $age_in_seconds / (60 * 60 * 24);

            if ($age_in_days > 1) {
                unlink($cache_filepath);
            }
            $cache_data = file_get_contents($cache_filepath);
        }

        if (!empty($cache_data)) {
            $cache_data = preg_split('/\r\n|\r|\n/', $cache_data);
            $cache_data = array_filter($cache_data);

            if ($is_visible_keys) {
                return $cache_data;
            }

            return count($cache_data);
        }

        $params_string = '';
        $params = [
            'q' => $domain,
            'api_key' => $this->api_key,
            'region' => 'gmsk',
            'num' => 1000,
        ];

        if (!$is_visible_keys) {
            $params['result_count'] = 1;
        }

        foreach ($params as $key => $param) {
            $params_string .= $key . '=' . $param . '&';
        }
        $params_string = trim($params_string, '&');

        $url = $this->url . '?' . $params_string;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Возвращает результат в виде строки

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Ошибка cURL: ' . curl_error($ch);
        }

        curl_close($ch);

        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }

        if (!empty($response) && $is_visible_keys) {
            file_put_contents($cache_filepath, $response);
        }
        if ($is_visible_keys) {
            $response = preg_split('/\r\n|\r|\n/', $response);
            $response = array_filter($response);
        }
        return $response;
    }

    protected function XMLGenerate($data)
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

        // Создание нового объекта таблицы (электронной таблицы)
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Заполнение таблицы данными из массива
        foreach ($data as $rowIndex => $row) {
            foreach ($row as $columnIndex => $cellData) {
                $sheet->setCellValueByColumnAndRow($columnIndex + 1, $rowIndex + 1, $cellData);
            }
        }

        // Создание XLSX файла
        $writer = new Xlsx($spreadsheet);
        $filename = $_SERVER['DOCUMENT_ROOT'] . '/last_output.xlsx';
        $writer->save($filename);
    }

    public function checkDomains(array $domains, $is_visible_keys = false)
    {
        $result = [];
        foreach ($domains as $domain) {
            $keys = $this->checkDomain($domain, $is_visible_keys);

            if (is_array($keys)) {
                $result[$domain]['keys'] = $keys;
                $result[$domain]['count_keys'] = count($keys);
            } else {
                $result[$domain]['count_keys'] = $keys;
            }
        }

        if (!empty($result)) {
            $xml_arr = [];
            foreach ($result as $domain => $d) {
                $xml_item = [
                    $domain,
                    $d['count_keys'],
                ];

                if (!empty($d['keys'])) {
                    $xml_item[] = implode("|", $d['keys']);
                }

                $xml_arr[] = $xml_item;
            }

            $this->XMLGenerate($xml_arr);
        }

        return $result;
    }
}
