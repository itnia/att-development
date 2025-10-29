<?php

const BASE_PATH = __DIR__;

include_once __DIR__ . '/autoload.php';
include_once __DIR__ . '/functions.php';

$response = new \AttReal\Http\PageResponse();

$response->setTitle('Page title ...');

echo $response->getContent();

# \Legacy\Http\HtmlResponse TODO: наследовать для \App\Http\HtmlResponse

# Делать такую проверку для организации нового кода и создания подмен
dump($response instanceof \AttReal\Http\HtmlResponse);

# Придумать единый роутинг - который работает в начале и можно получить в разных местах чтобы в конечном счете вызвать контроллер
# App\Http\Controllers\ - выделить только сам сайт остальное перенести в другие проекты
#  - сам портал только как вывод информации и не более
#  - делать постепенно подмену на новую реализацию















