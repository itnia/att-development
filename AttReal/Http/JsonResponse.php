<?php

namespace AttReal\Http;

//class JsonResponse
//{
//    /**
//     * Отправка JSON ответа с конвертацией в CP-1251
//     */
//    public static function send($data, $options = JSON_UNESCAPED_UNICODE, $encodeTo = 'CP1251')
//    {
//        // Конвертируем данные в UTF-8 если нужно
//        $convertedData = self::convertToUtf8($data);
//
//        // Кодируем в JSON
//        $json = json_encode($convertedData, $options);
//
//        if (json_last_error() !== JSON_ERROR_NONE) {
//            throw new \RuntimeException('JSON encoding error: ' . json_last_error_msg());
//        }
//
//        // Конвертируем JSON строку в нужную кодировку
//        if ($encodeTo && strtoupper($encodeTo) !== 'UTF-8') {
//            $json = iconv('UTF-8', $encodeTo . '//IGNORE', $json);
//        }
//
//        // Устанавливаем заголовки
//        self::setHeaders($encodeTo);
//
//        // Выводим результат
//        echo $json;
//
//        return $json;
//    }
//
//    /**
//     * Успешный JSON ответ
//     */
//    public static function success($data = null, $message = '', $code = 200)
//    {
//        $response = [
//            'success' => true,
//            'code' => $code,
//            'message' => $message,
//            'data' => $data,
//            'timestamp' => time()
//        ];
//
//        http_response_code($code);
//        return self::send($response);
//    }
//
//    /**
//     * Ошибочный JSON ответ
//     */
//    public static function error($message = '', $code = 400, $errors = [])
//    {
//        $response = [
//            'success' => false,
//            'code' => $code,
//            'message' => $message,
//            'errors' => $errors,
//            'timestamp' => time()
//        ];
//
//        http_response_code($code);
//        return self::send($response);
//    }
//
//    /**
//     * Конвертация данных в UTF-8
//     */
//    protected static function convertToUtf8($data)
//    {
//        if (is_array($data)) {
//            return array_map([self::class, 'convertToUtf8'], $data);
//        }
//
//        if (is_object($data)) {
//            $data = (array) $data;
//            return array_map([self::class, 'convertToUtf8'], $data);
//        }
//
//        if (is_string($data)) {
//            // Определяем кодировку
//            $encoding = mb_detect_encoding($data, ['UTF-8', 'CP1251', 'ISO-8859-1', 'Windows-1251'], true);
//
//            if ($encoding && $encoding !== 'UTF-8') {
//                return iconv($encoding, 'UTF-8//IGNORE', $data);
//            }
//        }
//
//        return $data;
//    }
//
//    /**
//     * Установка HTTP заголовков
//     */
//    protected static function setHeaders($encoding)
//    {
//        header('Content-Type: application/json; charset=' . $encoding);
//        header('Access-Control-Allow-Origin: *');
//        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
//        header('Access-Control-Allow-Headers: Content-Type, Authorization');
//
//        // Кэширование
//        header('Cache-Control: no-cache, must-revalidate');
//        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
//    }
//
//    /**
//     * Отправка сырых JSON данных
//     */
//    public static function raw($data, $encodeTo = 'CP1251')
//    {
//        return self::send($data, JSON_UNESCAPED_UNICODE, $encodeTo);
//    }
//
//    /**
//     * JSONP ответ для кросс-доменных запросов
//     */
//    public static function jsonp($data, $callback = 'callback', $encodeTo = 'CP1251')
//    {
//        $json = self::send($data, JSON_UNESCAPED_UNICODE, $encodeTo, false);
//        $response = $callback . '(' . $json . ');';
//
//        header('Content-Type: application/javascript; charset=' . $encodeTo);
//        echo $response;
//
//        return $response;
//    }
//
//    /**
//     * Проверка кодировки строки
//     */
//    public static function detectEncoding($string)
//    {
//        return mb_detect_encoding($string, ['UTF-8', 'CP1251', 'ISO-8859-1', 'Windows-1251'], true);
//    }
//
//    /**
//     * Массовая конвертация массива в UTF-8
//     */
//    public static function convertArrayToUtf8($array)
//    {
//        return self::convertToUtf8($array);
//    }
//}

class JsonResponse
{
    private $data;
    private $statusCode = 200;

    public function __construct($data, $statusCode = 200) {
        $this->statusCode = $statusCode;
        $this->data = $data;
    }

    public function send()
    {
        http_response_code($this->statusCode);
        // header('Content-Type: application/json; charset=cp1251');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->convertToUtf8($this->data), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Конвертация данных в UTF-8
     */
    protected function convertToUtf8($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'convertToUtf8'], $data);
        }

        if (is_object($data)) {
            $data = (array) $data;
            return array_map([self::class, 'convertToUtf8'], $data);
        }

        if (is_string($data)) {
            // Определяем кодировку
            $encoding = mb_detect_encoding($data, ['UTF-8', 'CP1251', 'ISO-8859-1', 'Windows-1251'], true);

            if ($encoding && $encoding !== 'UTF-8') {
                // return iconv($encoding, 'UTF-8', $data);
                // Преобразовываем всегда из cp1251
                return iconv('CP1251', 'UTF-8', $data);
            }
        }

        return $data;
    }
}
