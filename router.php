<?php

class LegacyRouter
{
    protected static $routes = [];
    protected static $currentRoute = null;
    protected static $defaultRoute = null;

    /**
     * Добавление маршрута
     */
    public static function AddRoute($routeKey, $handler, $isDefault = false)
    {
        self::$routes[$routeKey] = $handler;

        if ($isDefault) {
            self::$defaultRoute = $routeKey;
        }
    }

    /**
     * Получение текущего маршрута из query string
     */
    public static function getCurrentRoute()
    {
        if (self::$currentRoute !== null) {
            return self::$currentRoute;
        }

        // Получаем первый параметр из query string
        $queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        parse_str($queryString, $queryParams);

        // Берем первый ключ из query string
        $routeKeys = array_keys($queryParams);
        $routeKey = !empty($routeKeys) ? $routeKeys[0] : null;

        // Если роут не найден, используем дефолтный
        if ($routeKey && isset(self::$routes[$routeKey])) {
            self::$currentRoute = $routeKey;
        } elseif (self::$defaultRoute) {
            self::$currentRoute = self::$defaultRoute;
        } else {
            self::$currentRoute = false;
        }

        return self::$currentRoute;
    }

    /**
     * Получение обработчика для текущего маршрута
     */
    public static function getCurrentHandler()
    {
        $route = self::getCurrentRoute();

        if ($route && isset(self::$routes[$route])) {
            return self::$routes[$route];
        }

        return null;
    }

    /**
     * Выполнение текущего маршрута
     */
    public static function execute()
    {
        $handler = self::getCurrentHandler();

        if (is_callable($handler)) {
            return call_user_func($handler);
        } elseif (is_string($handler) && class_exists($handler)) {
            $instance = new $handler();
            if (method_exists($instance, 'handle')) {
                return $instance->handle();
            }
        } elseif (is_string($handler)) {
            return $handler;
        }

        return "Route not found";
    }

    /**
     * Получение всех параметров для текущего маршрута
     */
    public static function getParams()
    {
        $queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        parse_str($queryString, $queryParams);

        $route = self::getCurrentRoute();
        if ($route && isset($queryParams[$route])) {
            return $queryParams[$route];
        }

        return [];
    }

    /**
     * Генерация URL для маршрута
     */
    public static function url($routeKey, $params = '')
    {
        if (!isset(self::$routes[$routeKey])) {
            return null;
        }

        $url = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : 'index.php';

        if (!empty($params)) {
            return $url . '?' . $routeKey . '=' . $params;
        }

        return $url . '?' . $routeKey;
    }

    /**
     * Получение списка всех маршрутов
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    /**
     * Проверка активного маршрута
     */
    public static function isRoute($routeKey)
    {
        return self::getCurrentRoute() === $routeKey;
    }

    /**
     * TODO: Незачем лучше использовать RedirectResponse
     *
     * @param $routeKey
     * @param $params
     * @return void
     */
    public static function redirect($routeKey, $params = '')
    {
        $url = self::url($routeKey, $params);
        if ($url) {
            header("Location: " . $url);
            exit;
        }
    }
}
