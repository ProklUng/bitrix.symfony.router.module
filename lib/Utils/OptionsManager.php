<?php

namespace Proklung\Symfony\Router\Utils;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use ProklUng\Module\Boilerplate\Options\ModuleManager;

/**
 * Class OptionsManager
 * @package Proklung\Symfony\Router\Utils
 *
 * @since 25.07.2021
 */
class OptionsManager
{
    /**
     * @var array $proxy Кэш.
     */
    private static $proxy = [];

    /**
     * @param string $key Опция.
     *
     * @return mixed
     * @throws ArgumentNullException | ArgumentOutOfRangeException
     */
    public static function option(string $key)
    {
        if (isset(static::$proxy[$key])) {
            return static::$proxy[$key];
        }
        $moduleManager = new ModuleManager(static::moduleId());

        static::$proxy[$key] = $moduleManager->get($key);

        return static::$proxy[$key];
    }

    /**
     * ID модуля.
     *
     * @return string
     */
    public static function moduleId() : string
    {
        return 'proklung.symfony.router';
    }
}