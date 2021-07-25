<?php

use Bitrix\Main\Loader;
use Proklung\Symfony\Router\Utils\Bootstrap;

Loader::registerAutoLoadClasses('proklung.symfony.router',
    [
        'proklung_symfony_router' => 'install/index.php',
    ]
);

$bootstrapModule = new Bootstrap();
$bootstrapModule->init();
