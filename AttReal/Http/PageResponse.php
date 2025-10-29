<?php


namespace AttReal\Http;

class PageResponse extends HtmlResponse
{
    /** @var string */
    private $layout = 'default';

    /** @var array */
    private $sections = [];

    /** @var array */
    private $components = [];

    /** @var array */
    private $meta = [];

    /** @var array{scripts: array, styles: array} */
    private $assets = [
        'scripts' => [],
        'styles' => []
    ];

    /** @var array */
    private $structuredData = [];

    /** @var array */
    private $breadcrumbs = [];

    /**
     * @param string $layout
     * @return void
     */
    public function __construct($layout = 'default')
    {
        $this->layout = $layout;
        $this->initializeDefaultMeta();
    }

    private function initializeDefaultMeta()
    {
        $this->meta = [
            'title' => '',
            'description' => '',
            'keywords' => '',
            'robots' => 'index, follow',
            'canonical' => null,
            'alternate' => [],
            'open_graph' => []
        ];
    }

    // ===== ОСНОВНЫЕ SEO МЕТОДЫ =====

    /**
     * @param array $meta
     * @return self
     */
    public function setMeta($meta)
    {
        $this->meta = array_merge($this->meta, $meta);
        return $this;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->meta['title'] = $title;
        return $this;
    }

    /**
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->meta['description'] = $description;
        return $this;
    }

    /**
     * @param string $keywords
     * @return self
     */
    public function setKeywords($keywords)
    {
        $this->meta['keywords'] = $keywords;
        return $this;
    }

    /**
     * @param string $directives
     * @return self
     */
    public function setRobots($directives)
    {
        $this->meta['robots'] = $directives;
        return $this;
    }

    /**
     * @param string $url
     * @return self
     */
    public function setCanonical($url)
    {
        $this->meta['canonical'] = $url;
        return $this;
    }

    /**
     * @param string $url
     * @param string $language
     * @param string $type
     * @return self
     */
    public function addAlternate($url, $language, $type = 'website')
    {
        $this->meta['alternate'][] = [
            'url' => $url,
            'language' => $language,
            'type' => $type
        ];
        return $this;
    }

    // ===== OPEN GRAPH =====

    // TODO: setOpenGraphMeta

    /**
     * @param string $title
     * @return self
     */
    public function setOgTitle($title)
    {
        $this->meta['open_graph']['og:title'] = $title;
        return $this;
    }

    /**
     * @param string $description
     * @return self
     */
    public function setOgDescription($description)
    {
        $this->meta['open_graph']['og:description'] = $description;
        return $this;
    }

    /**
     * @param self $type
     * @return $this
     */
    public function setOgType($type)
    {
        $this->meta['open_graph']['og:type'] = $type;
        return $this;
    }

    /**
     * @param string $imageUrl
     * @param int|null $width
     * @param int|null $height
     * @return self
     */
    public function setOgImage($imageUrl, $width = null, $height = null)
    {
        $this->meta['open_graph']['og:image'] = $imageUrl;
        if ($width) $this->meta['open_graph']['og:image:width'] = $width;
        if ($height) $this->meta['open_graph']['og:image:height'] = $height;
        return $this;
    }

    /**
     * @param string $url
     * @return self
     */
    public function setOgUrl($url)
    {
        $this->meta['open_graph']['og:url'] = $url;
        return $this;
    }

    /**
     * @param string $siteName
     * @return self
     */
    public function setOgSiteName($siteName)
    {
        $this->meta['open_graph']['og:site_name'] = $siteName;
        return $this;
    }

    /**
     * @param string $locale
     * @return self
     */
    public function setOgLocale($locale)
    {
        $this->meta['open_graph']['og:locale'] = $locale;
        return $this;
    }

    // ===== СТРУКТУРИРОВАННЫЕ ДАННЫЕ =====

    /**
     * @param array $data
     * @return self
     */
    public function addStructuredData($data)
    {
        $this->structuredData[] = $data;
        return $this;
    }

    /**
     * Установить хлебные крошки.
     *
     * @param array<int, array{
     *     name: string,
     *     url: string
     * }> $items
     * @return self
     */
    public function setBreadcrumbList($items)
    {
        $this->structuredData[] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(function ($index, $item) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url']
                ];
            }, array_keys($items), $items)
        ];

        $this->breadcrumbs = $items;
        return $this;
    }

    // ===== КОМПОНЕНТЫ И СЕКЦИИ =====

//    public function addComponent(Component $component, string $section = 'content', int $priority = 50)
//    {
//        $this->components[$section][] = [
//            'component' => $component,
//            'priority' => $priority
//        ];
//
//        // Автоматически собираем ассеты компонента
//        $this->collectComponentAssets($component);
//        return $this;
//    }

    /**
     * @param string $name
     * @param string $content
     * @param int $priority
     * @return self
     */
    public function addSection($name, $content, $priority = 50)
    {
        $this->sections[$name][] = [
            'content' => $content,
            'priority' => $priority
        ];
        return $this;
    }

    /**
     * @param string $templatePath
     * @param array $data
     * @param string $section
     * @param int $priority
     * @return self
     */
    public function addTemplate($templatePath, $data = [], $section = 'content', $priority = 50)
    {
        $content = $this->renderTemplate($templatePath, $data);
        return $this->addSection($section, $content, $priority);
    }

    // ===== АССЕТЫ =====

    /**
     * @param string $src
     * @param string $position
     * @param array $attrs
     * @return self
     */
    public function addScript($src, $position = 'footer', $attrs = [])
    {
        $this->assets['scripts'][$position][] = [
            'src' => $src,
            'attrs' => $attrs
        ];
        return $this;
    }

    /**
     * @param string $code
     * @param string $position
     * @return self
     */
    public function addInlineScript($code, $position = 'footer')
    {
        $this->assets['scripts'][$position][] = [
            'inline' => $code,
            'attrs' => []
        ];
        return $this;
    }

    /**
     * @param string $href
     * @param array $attrs
     * @return self
     */
    public function style($href, $attrs = [])
    {
        // TODO: избавление от попаданий
//        $isLocalPath = true;
//        foreach (['http://', 'https://', '//'] as $needle) {
//            if (str_starts_with($href, $needle)) {
//                $isLocalPath = false;
//                break;
//            }
//        }
//
//        if ($isLocalPath) {
//            // $href - добавить метку на последнее изменение
//        }

        $this->assets['styles'][] = [
            'href' => $href,
            'attrs' => $attrs
        ];
        return $this;
    }

    // ===== РЕНДЕРИНГ =====

    /**
     * @return string
     */
    public function getContent()
    {
        $this->renderComponentsIntoSections();
        return $this->renderLayout();
    }

    /**
     * @return void
     */
    private function renderComponentsIntoSections()
    {
//        foreach ($this->components as $section => $sectionComponents) {
//            // Сортируем по приоритету
//            usort($sectionComponents, fn($a, $b) => $b['priority'] <=> $a['priority']);
//
//            // Рендерим все компоненты секции
//            $content = '';
//            foreach ($sectionComponents as $item) {
//                $content .= $item['component']->render();
//            }
//
//            $this->addSection($section, $content);
//        }
    }

    /**
     * @return string
     */
    private function renderLayout()
    {
        // Подготавливаем все данные для layout
        $layoutData = [
            'sections' => $this->getSortedSections(),
            'meta' => $this->meta,
            'assets' => $this->assets, // TODO: можно нормализовать чтобы нормально вывести - или просто использовать helper
            'breadcrumbs' => $this->breadcrumbs,
            'structuredData' => $this->structuredData
        ];

        return $this->renderTemplate("layouts/{$this->layout}.php", $layoutData);
    }

    /**
     * @return array
     */
    private function getSortedSections()
    {
        $result = [];

        foreach ($this->sections as $sectionName => $sectionItems) {
            foreach ($sectionItems as $index => &$item) {
                $item['__original_index'] = $index;
            }

            usort($sectionItems, function($a, $b) {
                if ($b['priority'] != $a['priority']) {
                    return ($b['priority'] < $a['priority']) ? -1 : 1;
                }
                if ($a['__original_index'] != $b['__original_index']) {
                    return ($a['__original_index'] < $b['__original_index']) ? -1 : 1;
                }
                return 0;
            });

            foreach ($sectionItems as &$item) {
                unset($item['__original_index']);
            }

            unset($item);

            $result[$sectionName] = '';
            foreach ($sectionItems as $item) {
                $result[$sectionName] .= $item['content'];
            }
        }

        return $result;
    }

//    private function collectComponentAssets(Component $component): void
//    {
//        // Собираем скрипты компонента
//        foreach ($component->getScripts() as $position => $scripts) {
//            foreach ($scripts as $script) {
//                $this->assets['scripts'][$position][] = $script;
//            }
//        }
//
//        // Собираем стили компонента
//        foreach ($component->getStyles() as $style) {
//            $this->assets['styles'][] = $style;
//        }
//    }

    /**
     * @param string $template
     * @param array $__
     * @return string
     */
    private function renderTemplate($template, $__)
    {
        extract($__);unset($__);
        ob_start();
        include BASE_PATH . "/views/{$template}";
        return ob_get_clean();
    }
}
