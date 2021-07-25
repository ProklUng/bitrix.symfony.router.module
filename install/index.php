<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use ProklUng\Module\Boilerplate\Module;
use ProklUng\Module\Boilerplate\ModuleUtilsTrait;

Loc::loadMessages(__FILE__);

class proklung_symfony_router extends CModule
{
    use ModuleUtilsTrait;

    public function __construct()
    {
        $arModuleVersion = [];

        include __DIR__.'/version.php';

        if (is_array($arModuleVersion)
            &&
            array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_FULL_NAME = 'symfony.router';
        $this->MODULE_VENDOR = 'proklung';
        $prefixLangCode = 'SYMFONY_ROUTER';

        $this->MODULE_NAME = Loc::getMessage($prefixLangCode.'_MODULE_NAME');
        $this->MODULE_ID = $this->MODULE_VENDOR.'.'.$this->MODULE_FULL_NAME;

        $this->MODULE_DESCRIPTION = Loc::getMessage($prefixLangCode.'_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage($prefixLangCode.'_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage($prefixLangCode.'MODULE_PARTNER_URI');

        $this->INSTALL_PATHS = [
            '/local/modules/proklung.symfony.router/install/admin/symfony_router_index.php'
            => '/bitrix/admin/symfony_router_index.php',
        ];

        $this->moduleManager = new Module(
            [
                'MODULE_ID' => $this->MODULE_ID,
                'VENDOR_ID' => $this->MODULE_VENDOR,
                'MODULE_VERSION' => $this->MODULE_VERSION,
                'MODULE_VERSION_DATE' => $this->MODULE_VERSION_DATE,
                'ADMIN_FORM_ID' => $this->MODULE_VENDOR.'_settings_form',
            ]
        );

        $this->moduleManager->addModuleInstance($this);
        $this->options();
    }

    /**
     * @inheritDoc
     */
    protected function getSchemaTabsAdmin(): array
    {
        $values =
            [
                'yaml_config' => [
                    'TAB' => 'Роуты Symfony',
                    'TITLE' => 'Роуты Symfony',
                ],
            ];

        if (self::checkRequirements()) {
            $values = array_merge(
                $values,
                [ 'bitrix_native_routes' => [
                    'TAB' => 'Нативные роуты Битрикс',
                    'TITLE' => 'Нативные роуты Битрикс',
                ]]
            );
        }

        return $values;
    }

    /**
     * @inheritDoc
     */
    protected function getSchemaOptionsAdmin(): array
    {
        return [
            'symfony_routes_active' =>
                [
                    'label' => 'Активность',
                    'tab' => 'yaml_config',
                    'type' => 'checkbox',
                ],
            'yaml_config_file_path' =>
                [
                    'label' => 'Путь к Yaml файлу с конфигурацией роутов',
                    'tab' => 'yaml_config',
                    'type' => 'text',
                ],

            'yaml_cache_path' =>
                [
                    'label' => 'Путь к папке с кэшом Yaml файлов',
                    'tab' => 'yaml_config',
                    'type' => 'text',
                ],
            'bitrix_routes_active' =>
                [
                    'label' => 'Активность',
                    'tab' => 'bitrix_native_routes',
                    'type' => 'checkbox',
                ],
            'php_router_config_path' =>
                [
                    'label' => 'Файл php с описанием роутов (в папке /local/routes)',
                    'tab' => 'bitrix_native_routes',
                    'type' => 'text',
                ],
            'native_config_file_path' =>
                [
                    'label' => 'Путь к Yaml файлу с конфигурацией нативных роутов Битрикс',
                    'tab' => 'bitrix_native_routes',
                    'type' => 'text',
                ],
            'native_yaml_cache_path' =>
                [
                    'label' => 'Путь к папке с кэшом Yaml файлов нативных роутов Битрикс',
                    'tab' => 'bitrix_native_routes',
                    'type' => 'text',
                ],

        ];
    }

    /**
     * Проверка на нужную версию главного модуля.
     *
     * @return boolean
     */
    private static function checkRequirements(): bool
    {
        return CheckVersion(ModuleManager::getVersion('main'), '21.400.0');
    }
}
