<?php

const BASE_PATH = __DIR__;

include_once __DIR__ . '/autoload.php';
include_once __DIR__ . '/functions.php';
include_once __DIR__ . '/router.php';

# ----------------------------------------------------------------------------------------------------------------------

# \Legacy\Http\HtmlResponse TODO: наследовать для \App\Http\HtmlResponse

# ----------------------------------------------------------------------------------------------------------------------

# TODO: использовать редирект для избавление определенных ключей

# ----------------------------------------------------------------------------------------------------------------------

# for 404 and home TODO: переделать так чтобы route `__` - был по умолчанию - и проводить там основную обработку
LegacyRouter::AddRoute('__', \App\Http\Controllers\Web\HomeController::class, true);

# ----------------------------------------------------------------------------------------------------------------------

LegacyRouter::AddRoute('news', \App\Http\Controllers\Web\TestController::class);

LegacyRouter::AddRoute('test', \App\Http\Controllers\Web\TestController::class);

# ----------------------------------------------------------------------------------------------------------------------

# example for articles
for ($i = 1; $i < 1000; ++$i) {
    LegacyRouter::AddRoute('Route_' . $i, \App\Http\Controllers\Web\TestController::class);
}

# ----------------------------------------------------------------------------------------------------------------------

# example for redirects TODO: можно делать любую логику в плоть до того что можно будет делать переход на новую архитектуру
for ($i = 1; $i < 1000; ++$i) {
    LegacyRouter::AddRoute('RouteRedirect_' . $i, function () use ($i) {
        header("Location: ?articles=" . str_replace('_', '-', strtolower('RouteRedirect_' . $i)));
        exit;
    });
}

# ----------------------------------------------------------------------------------------------------------------------

# TODO: цель сохранить ссылки для управляемости - но перестроить логику выполнения кода
#  - постепенно выпилить старый routing через новую реализацию
#  - через флаг определять какая сейчас реализация действует

# ----------------------------------------------------------------------------------------------------------------------

# example: JSON с преобразованием в utf8 - делать контроль по обработке данных
LegacyRouter::AddRoute('json', function () {
    header('Access-Control-Allow-Origin: *');

    $text = 'Теперь отдых и оздоровление на живописном берегу озера Нарочь стали ещё доступнее!';
    $text = mb_convert_encoding($text, 'windows-1251', 'utf-8');

    $response = new \AttReal\Http\SuccessJsonResponse(['test' => $text]);
    $response->send();
    exit();
});

# ----------------------------------------------------------------------------------------------------------------------

$response = LegacyRouter::execute();
if (is_string($response)) {
    echo $response;
} elseif ($response instanceof \AttReal\Http\PageResponse) {
    // TODO: add headers, ... | + остальные данные
    header('Content-type: text/html; charset=cp1251');
    echo $response->getContent();
}

# ----------------------------------------------------------------------------------------------------------------------

# TODO: новая архитектура на utf8 - но пока временно использовать конвертацию response в cp1251 - но обработку делать в utf8
#  - логику переносить + Gateway/Adapter - для обратного вызова - заместить глобальные переменные
























