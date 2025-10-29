<?php

namespace App\Http;

use AttReal\Http\PageResponse as BasePageResponse;

class PageResponse extends BasePageResponse
{
    public static function forPublic()
    {
        // TODO: также можно зависимости от скриптов реализовать здесь

        return (new self);
    }
}
