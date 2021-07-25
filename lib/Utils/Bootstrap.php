<?php

namespace Proklung\Symfony\Router\Utils;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\EventManager;
use ProklUng\Module\Boilerplate\Options\ModuleManager;
use Proklung\Symfony\Router\Router;
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
     * @var ModuleManager $moduleManager
     */
    private $moduleManager;

    /**
     * @var Router|null $symfonyRouterInstance
     */
    private $symfonyRouterInstance;

    /**
     * @var BitrixRouteConvertor|null $bitrixRouteConvertor
     */
    private $bitrixRouteConvertor;

    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $this->moduleManager = new ModuleManager('proklung.symfony.router');
    }

    /**
     * Инициализация хозяйства.
     *
     * @return void
     * @throws ArgumentNullException | ArgumentOutOfRangeException Ошибки аргументов модуля.
     */
    public function init() : void
    {
        $this->initEvents();
        $this->initSymfonyRoutes();
        $this->initNativeBitrixRoutes();
    }

    /**
     * Инициализация роутов Symfony.
     *
     * @return void
     * @throws ArgumentNullException | ArgumentOutOfRangeException Ошибки аргументов модуля.
     */
    private function initSymfonyRoutes() : void
    {
        $configFilePath = $this->moduleManager->get('yaml_config_file_path');
        $cachePath =  $this->moduleManager->get('yaml_cache_path');

        $this->symfonyRouterInstance = Loader::from($configFilePath, $cachePath);
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
            $configBitrixRoutesPath = $this->moduleManager->get('native_config_file_path');
            $cacheBitrixRoutesPath =  $this->moduleManager->get('native_yaml_cache_path');
            $phpConfigFile =  $this->moduleManager->get('php_router_config_path');

            $this->bitrixRouteConvertor = Loader::native($configBitrixRoutesPath, $cacheBitrixRoutesPath);
            if ($this->bitrixRouteConvertor !== null) {
                Loader::save($phpConfigFile, $this->bitrixRouteConvertor);
            }
        }
    }

    /**
     * @return void
     */
    private function initEvents() : void
    {
        EventManager::getInstance()->addEventHandler(
            $this->moduleManager->getModuleId(),
            'OnAfterSaveOptions',
            [new OnAfterSaveOptionsHandler, 'handler']
        );

        EventManager::getInstance()->addEventHandler(
            $this->moduleManager->getModuleId(),
            'OnBeforeSetOption',
            [new ValidatorNativeConfigs, 'handler']
        );

        EventManager::getInstance()->addEventHandler(
            $this->moduleManager->getModuleId(),
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