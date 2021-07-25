<?php

return [
    [
        'parent_menu' => 'global_menu_content',
        'sort' => 400,
        'text' => 'Настройка роутера Symfony',
        'title' => 'Настройка роутера Symfony',
        'url' => 'symfony_router_index.php',
        'items_id' => 'menu_references',
        'items' => [
                [
                    'text' => 'Конфигурация',
                    'url' => 'symfony_router_index.php',
                    'more_url' => ['symfony_router_index.php'],
                    'title' => 'Конфигурация',
                ],
        ],
    ],
];
