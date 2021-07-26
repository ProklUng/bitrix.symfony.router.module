<?php

namespace Proklung\Symfony\Router\Utils;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\EventManager;
use Proklung\Symfony\Router\Subscribers\OnAfterSaveOptionsHandler;
use Proklung\Symfony\Router\Subscribers\ValidatorNativeConfigs;
use Proklung\Symfony\Router\Subscribers\ValidatorSymfonyConfigs;

/***
 * Class Bootstrap
 * @package Proklung\Symfony\Router\Utils
 *
 * @since 25.07.2021
 */
class Bootstrap
{
    /**
     * @return void
     * @throws ArgumentNullException | ArgumentOutOfRangeException Ошибки аргументов модуля.
     */
    public function init() : void
    {
        $this->initEvents();

        if (OptionsManager::option('symfony_routes_active')) {
            $this->initSymfonyRoutes();
        }

        if (OptionsManager::option('bitrix_routes_active')) {
            $this->initNativeBitrixRoutes();
        }
    }

    /**
     * Инициализация роутов Symfony.
     *
     * @return void
     * @throws ArgumentNullException | ArgumentOutOfRangeException Ошибки аргументов модуля.
     */
    private function initSymfonyRoutes() : void
    {
        $configFilePath = OptionsManager::option('yaml_config_file_path');
        $cachePath =  OptionsManager::option('yaml_cache_path');

        Loader::from($configFilePath, $cachePath);
    }

    /**
     * Инициализация нативных битриксовых роутов.
     *
     * @return void
     * @throws ArgumentNullException | ArgumentOutOfRangeException Ошибки аргументов модуля.
     */
    private function initNativeBitrixRoutes() : void
    {
        if ($this->checkRequirements()) {
            $configBitrixRoutesPath = OptionsManager::option('native_config_file_path');
            $cacheBitrixRoutesPath =  OptionsManager::option('native_yaml_cache_path');
            $phpConfigFile =  OptionsManager::option('php_router_config_path');

            $routeConvertor = Loader::native($configBitrixRoutesPath, $cacheBitrixRoutesPath);
            if ($routeConvertor !== null) {
                Loader::save($phpConfigFile, $routeConvertor);
            }
        }
    }

    /**
     * Инициализация событий модуля.
     *
     * @return void
     */
    private function initEvents() : void
    {
        EventManager::getInstance()->addEventHandler(
            OptionsManager::moduleId(),
            'OnAfterSaveOptions',
            [new OnAfterSaveOptionsHandler, 'handler']
        );

        EventManager::getInstance()->addEventHandler(
            OptionsManager::moduleId(),
            'OnBeforeSetOption',
            [new ValidatorNativeConfigs, 'handler']
        );

        EventManager::getInstance()->addEventHandler(
            OptionsManager::moduleId(),
            'OnBeforeSetOption',
            [new ValidatorSymfonyConfigs, 'handler']
        );
    }

    /**
     * Проверка на нужную версию главного модуля.
     *
     * @return boolean
     */
    private function checkRequirements(): bool
    {
        return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '21.400.0');
    }
}