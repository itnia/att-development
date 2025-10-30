<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\PageResponse;

class TestController extends Controller
{
    public function handle()
    {
        // TODO: формат возврата уже от HTTP_ACCEPT, CONTENT_TYPE
        if ($this->isAjax()) {
            // TODO:
            return '';

            // TODO: вместо fetch использовать axios
            // TODO: не полагаться на ajax - а определить другую концепцию где запрашивается json явно - чтобы отдать правильный результат
            // - для ajax оставить только логику через queryParam - ajax - или использовать axios - для нормальной работы
            // - или реализовать обертку над fetch - и использовать для написания логики
        }

        // TODO: кеш отдельно использовать внутри
        return $this->showPage();
    }

    protected function showPage()
    {
        dump(self::class);
        dump($this->getRouteKey());
        dump($this->getRouteParam());

        // ...
        $newsId = (int)$this->getRouteParam();
        $newsItem = $this->getNewsById($newsId);
        if (is_null($newsItem)) {
            return '';
        }

        // TODO: в /data/base/index.htm - делать перехват и смотреть через флаг были данные возращены через контроллер - логику реализовать в роутере
        //  который вызывать в этом файле и проверить на подходящий route - если нет то оборачивать в PageResponse и реализовать вынос всех данных

        $response = PageResponse::forPublic();

        $response->setTitle('Test ...');
        $response->addSection('content', '<div>aaa</div>');
        $response->addSection('content', '<div>bbb</div>');
        $response->addSection('content', '<div>ссс</div>');

        return $response;
    }

    protected function getNewsById($id)
    {
        return [];
    }

    protected function getNewsByAlias($alias)
    {
        return [];
    }
}
