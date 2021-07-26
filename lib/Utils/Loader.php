<?php

namespace Proklung\Symfony\Router\Utils;

use Proklung\Symfony\Router\BitrixInitializerRouter;
use Proklung\Symfony\Router\BitrixRoutes;
use Proklung\Symfony\Router\Router;
use Proklung\Symfony\Router\SymfonyRoutes;

/**
 * Class Loader
 * @package Proklung\Symfony\Router\Utils
 *
 * @since 25.07.2021
 */
class Loader
{
    /**
     * @var array $instances Cache.
     */
    private static $instances = [];

    /**
     * @param string               $fileName             Имя файла.
     * @param BitrixRouteConvertor $bitrixRouteConvertor Конвертор.
     *
     * @return void
     */
    public static function save(string $fileName, BitrixRouteConvertor $bitrixRouteConvertor) : void
    {
        static::$instances[md5($fileName)] = $bitrixRouteConvertor;
    }

    /**
     * @param string $fileName Имя файла.
     *
     * @return BitrixRouteConvertor|null
     */
    public static function get(string $fileName) : ?BitrixRouteConvertor
    {
        $key = md5($fileName);
        if (isset(static::$instances[$key])) {
            return static::$instances[$key];
        }

        return null;
    }

    /**
     * Статический фасад для загрузки нативных битриксовых роутов.
     *
     * @param string|null $yamlConfig Yaml конфиг.
     * @param string|null $cachePath  Путь к кэшу. Null -> не кешировать.
     *
     * @return BitrixRouteConvertor|null
     */
    public static function native(?string $yamlConfig = null, ?string $cachePath = null) : ?BitrixRouteConvertor
    {
        $hash = md5((string)$yamlConfig . (string)$cachePath);
        if (isset(static::$instances[$hash])) {
            return static::$instances[$hash];
        }

        $self = new static();

        return static::$instances[$hash] = $self->loadRouterNative($yamlConfig, $cachePath);
    }

    /**
     * Статический фасад для загрузки роутов Symfony.
     *
     * @param string|null $yamlConfig Yaml конфиг.
     * @param string|null $cachePath  Путь к кэшу. Null -> не кешировать.
     *
     * @return Router|null
     */
    public static function from(?string $yamlConfig = null, ?string $cachePath = null) : ?Router
    {
        $hash = md5((string)$yamlConfig . (string)$cachePath);
        if (isset(static::$instances[$hash])) {
            return static::$instances[$hash];
        }

        $self = new static();

        return static::$instances[$hash] = $self->loadRouter($yamlConfig, $cachePath);
    }

    /**
     * Загрузить роутер.
     *
     * @param string|null $yamlConfig Yaml конфиг.
     * @param string|null $cachePath  Путь к кэшу. Null -> не кешировать.
     *
     * @return Router|null
     */
    public function loadRouter(?string $yamlConfig = null, ?string $cachePath = null) : ?Router
    {
        if ($yamlConfig && @file_exists($_SERVER['DOCUMENT_ROOT'] . $yamlConfig)) {
            $agnosticRouter = new SymfonyRoutes(
                $_SERVER['DOCUMENT_ROOT'] . $yamlConfig,
                $cachePath ? $_SERVER['DOCUMENT_ROOT'] . $cachePath : null,
                (bool)$_ENV['DEBUG']
            );

            return new Router(
                $agnosticRouter->getRouter(),
                new BitrixInitializerRouter()
            );
        }

        return null;
    }

    /**
     * Загрузить роутер для нативных битриксовых маршрутов.
     *
     * @param string|null $yamlConfig Yaml конфиг.
     * @param string|null $cachePath  Путь к кэшу. Null -> не кешировать.
     *
     * @return BitrixRouteConvertor|null
     */
    public function loadRouterNative(?string $yamlConfig = null, ?string $cachePath = null) : ?BitrixRouteConvertor
    {
        if ($yamlConfig && @file_exists($_SERVER['DOCUMENT_ROOT'] . $yamlConfig)) {
            $agnosticRouter = new BitrixRoutes(
                $_SERVER['DOCUMENT_ROOT'] . $yamlConfig,
                $cachePath ? $_SERVER['DOCUMENT_ROOT'] . $cachePath : null,
                (bool)$_ENV['DEBUG']
            );

            $routeCollection = $agnosticRouter->getRoutes();

            return new BitrixRouteConvertor($routeCollection);
        }

        return null;
    }
}