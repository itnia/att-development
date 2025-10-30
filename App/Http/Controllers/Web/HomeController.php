<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Http\PageResponse;

class HomeController extends Controller
{
    function handle()
    {
        # fix для защиты от игнорирования стоп слов
        $queryStringFirstKey = $this->getQueryStringFirstKey();
        if ($queryStringFirstKey && !in_array($queryStringFirstKey, ['showinfo', 'traceme'])) {
            header("Location: /");
            exit;
        }

        $response = PageResponse::forPublic();

        $response->setTitle('Home page');
        $response->addSection('content', '<div>aaa</div>');

        return $response;
    }

    protected function getQueryStringFirstKey()
    {
        $queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        parse_str($queryString, $queryParams);
        $routeKeys = array_keys($queryParams);
        return !empty($routeKeys) ? $routeKeys[0] : null;
    }
}
