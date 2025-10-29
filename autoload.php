<?php

spl_autoload_register(function ($class) {
    $path = __DIR__ . DIRECTORY_SEPARATOR . str_replace(array('\\', '/', '//'), DIRECTORY_SEPARATOR, $class . '.php');
    if (is_file($path)) {
        require_once $path;
    }
});
