<?php

namespace Proklung\Symfony\Router\Contracts;

use Proklung\Symfony\Router\InitRouter;

/**
 * Interface RouterInitializerInterface
 * @package Proklung\Symfony\Router\Contracts
 *
 * @since 24.07.2021
 */
interface RouterInitializerInterface
{
    /**
     * Инициализация роутера.
     *
     * @param InitRouter $router Инициализированный роутер.
     *
     * @return mixed
     */
    public function init(InitRouter $router);
}