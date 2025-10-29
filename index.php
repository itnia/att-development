<?php

const BASE_PATH = __DIR__;

include_once __DIR__ . '/autoload.php';
include_once __DIR__ . '/functions.php';



# \Legacy\Http\HtmlResponse TODO: наследовать для \App\Http\HtmlResponse

# Придумать единый роутинг - который работает в начале и можно получить в разных местах чтобы в конечном счете вызвать контроллер
# App\Http\Controllers\ - выделить только сам сайт остальное перенести в другие проекты
#  - сам портал только как вывод информации и не более
#  - делать постепенно подмену на новую реализацию



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
}



// 1. Регистрация маршрутов
LegacyRouter::AddRoute('page', function() {
    $params = LegacyRouter::getParams();
    $pageId = is_array($params) ? (isset($params[0]) ? $params[0] : 'home') : $params;
    return "Showing page: " . htmlspecialchars($pageId);
});

LegacyRouter::AddRoute('news', \App\Http\Controllers\Web\TestController::class);


$response = LegacyRouter::execute();
if (is_string($response)) {
    echo $response;
} elseif ($response instanceof \AttReal\Http\PageResponse) {
    echo $response->getContent();
}

