<?php

namespace App\Http\Controllers;

abstract class Controller
{
    // TODO: реализовать набор методов для управляемости

    abstract function handle();

    # TODO: переделать так чтобы получать данные в json - можно новый методы isJsonResponse
    protected function isAjax($checkContentType = true)
    {
        // 1. Основная проверка - HTTP заголовок X-Requested-With
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $value = strtolower(trim($_SERVER['HTTP_X_REQUESTED_WITH']));
            if ($value === 'xmlhttprequest') {
                return true;
            }
        }

        // 2. Проверка HTTP заголовка X-PJAX (для pjax запросов)
        if (isset($_SERVER['HTTP_X_PJAX'])) {
            $value = strtolower(trim($_SERVER['HTTP_X_PJAX']));
            if ($value === 'true') {
                return true;
            }
        }

        if ($checkContentType) {
            // Проверка по Accept заголовку
            if (isset($_SERVER['HTTP_ACCEPT'])) {
                $accept = strtolower(trim($_SERVER['HTTP_ACCEPT']));
                if (strpos($accept, 'application/json') !== false ||
                    strpos($accept, 'application/javascript') !== false ||
                    strpos($accept, 'text/javascript') !== false) {
                    return true;
                }
            }

            // Проверка по Content-Type для POST запросов
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $contentType = strtolower(trim($_SERVER['CONTENT_TYPE']));
                if (strpos($contentType, 'application/json') !== false ||
                    strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
                    return true;
                }
            }
        }

        if (isset($_REQUEST['ajax'])) {
            return true;
        }

        return false;
    }

    // TODO: заменить на expectsJson с проверкой это ли ajax
    public function wantsJson()
    {
        if (!isset($_SERVER['HTTP_ACCEPT'])) {
            return false;
        }

        $accept = strtolower(trim($_SERVER['HTTP_ACCEPT']));
        if (strpos($accept, 'application/json') !== false || strpos($accept, '+json') !== false) {
            return true;
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
            if (strpos($uri, '/api/') === 0 || strpos($uri, '/ajax/') === 0) {
                return true;
            }
        }

        return false;
    }

    protected function getRouteKey()
    {
        $key = \LegacyRouter::getCurrentRoute();
        return $key !== '__' ? $key : null;
    }

    protected function getRouteParam()
    {
        $params = \LegacyRouter::getParams();
        return isset($params[0]) ? $params[0] : null;
    }
}
