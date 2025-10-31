# TODO: таблица для cms системы с явной логикой для выстраивания
#  ---------------------------------------------------------------------------------------------------------------------
#  - отдельная таблица pages - для выстраивания специализированных страниц - и связанная также с этой таблицей или отдельная логика хоть с идентичными сущностями
#  - entity_type => sanatorii.infrastructure (хранить связку проект и сущность) - или выделить в entity_project для разделение логики
#    - entity_type = __.page - для всех проектов или просто - page - хотя в самих станицах держать логику связи с проектами
#    хотя нет сильной необходимости так как будет массив сопоставлений
#  ---------------------------------------------------------------------------------------------------------------------
#  - Использование для специфичных страниц - нужно составить список примеров
#    - пример: инфраструктура - для создания страниц с карточками предложений (shortcode) и описанием
#    - привязка к страницам для расширения
#  ---------------------------------------------------------------------------------------------------------------------
#  - slug|alias - данная таблица не зависит от url построения, сами страницы только должны быть c иерархией
#    - это вопрос все ли отображать например инфраструктуры - кто управляет - это вопрос страниц а ни сущности
#      но с другой стороны - на основе количества инфраструктур делать вывод
#      [ВОПРОС УПРОВЛЯЕМОСТИ - если сущность там alias у сущности или использовать другую логику - посмотреть актуальность ЧПУ и как реализация у других]
#    - прежде всего это нужно для создания новых страниц
#    - может контент в сами сущности запихнуть - НЕТ ДЛЯ САЙТА ВАЖНЫ СТРАНЦИЦЫ - И ИХ РЕАЛИЗАЦИЯ НУЖНА
#     slug - будет уже влиять только с учетом unique(slug, entity_type, entity_id)
#
#    ...
#    ОПРЕДЕЛИТЬСЯ С СПИСКОМ СТРАНИЦ - ДЕРЕВО СТРАНИЦ
#    ...
#
#
#
create table entity_pages
(
    id                  int unsigned auto_increment primary key,
    slug                varchar(255) unique not null,
    title_ru            varchar(255)                            default '',
    title_en            varchar(255)                            default '',
    title_be            varchar(255)                            default '',
    content_ru          mediumtext                              default '',
    content_en          mediumtext                              default '',
    content_be          mediumtext                              default '',
    excerpt_ru          text                                    default '',
    excerpt_en          text                                    default '',
    excerpt_be          text                                    default '',
    meta_title_ru       varchar(255)                            default '',
    meta_title_en       varchar(255)                            default '',
    meta_title_be       varchar(255)                            default '',
    meta_description_ru text                                    default '',
    meta_description_en text                                    default '',
    meta_description_be text                                    default '',
    status              ENUM ('draft', 'published', 'archived') default 'draft',
    entity_id           int                                     default 0,
    entity_type         varchar(255)                            default '',
    -- Составной уникальный индекс
    UNIQUE KEY unique_entity (entity_type, entity_id)
)
    comment 'Страницы сущностей' collate = utf8_general_ci;