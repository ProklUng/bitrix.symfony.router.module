<?php

use Bitrix\Main\Loader;
use ProklUng\Module\Boilerplate\Options\ModuleManager;
use Proklung\Symfony\Router\Subscribers\OnAfterSaveOptionsHandler;
use Proklung\Symfony\Router\Subscribers\ValidatorNativeConfigs;
use Proklung\Symfony\Router\Subscribers\ValidatorSymfonyConfigs;

Loader::registerAutoLoadClasses(
    'proklung.symfony.router',
    [
        'proklung_symfony_router' => 'install/index.php',
    ]
);

$moduleManager = new ModuleManager('proklung.symfony.router');
$configFilePath = $moduleManager->get('yaml_config_file_path');
$cachePath =  $moduleManager->get('yaml_cache_path');

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    $moduleManager->getModuleId(),
    'OnAfterSaveOptions',
    [new OnAfterSaveOptionsHandler, 'handler']
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    $moduleManager->getModuleId(),
    'OnBeforeSetOption',
    [new ValidatorNativeConfigs, 'handler']
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    $moduleManager->getModuleId(),
    'OnBeforeSetOption',
    [new ValidatorSymfonyConfigs, 'handler']
);

$routerInstance = \Proklung\Symfony\Router\Utils\Loader::from($configFilePath, $cachePath);

if (Proklung\Symfony\Router\Utils\Loader::checkRequirements()) {
    $configBitrixRoutesPath = $moduleManager->get('native_config_file_path');
    $cacheBitrixRoutesPath =  $moduleManager->get('native_yaml_cache_path');
    $phpConfigFile =  $moduleManager->get('php_router_config_path');

    $routeConvertor = Proklung\Symfony\Router\Utils\Loader::native($configBitrixRoutesPath, $cacheBitrixRoutesPath);
    if ($routeConvertor !== null) {
        Proklung\Symfony\Router\Utils\Loader::save($phpConfigFile, $routeConvertor);
    }
}
