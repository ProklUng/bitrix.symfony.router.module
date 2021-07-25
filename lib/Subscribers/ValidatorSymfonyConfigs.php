<?php

namespace Proklung\Symfony\Router\Subscribers;

use Bitrix\Main\Event;
use Exception;
use Proklung\Symfony\Router\Utils\OptionsManager;

/**
 * Class ValidatorSymfonyConfigs
 * @package Proklung\Symfony\Router\Subscribers
 *
 * @since 25.07.2021
 */
class ValidatorSymfonyConfigs
{
    /**
     * Обработка наличия-отсутствия Yaml конфига нативных роутов Битрикс.
     *
     * @param Event $event Событие.
     *
     * @return void
     * @throws Exception
     */
    public function handler(Event $event): void
    {
        $params = $event->getParameters();
        if ($params['name'] !== 'yaml_config_file_path' || !OptionsManager::option('symfony_routes_active')) {
            return;
        }

        if (!$params['value']) {
            require_once($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/interface/admin_lib.php');
            \CAdminMessage::showMessage([
                'MESSAGE' => 'Если не указать файла с Yaml конфигом Symfony роутов, то конструкция отключается.',
                'TYPE' => 'Warning',
            ]);
        }

        if ($params['value'] && !@file_exists($_SERVER['DOCUMENT_ROOT'].$params['value'])) {
            throw new Exception(
                'Файл с конфигом Symfony роутов' . $params['event'].' не существует. Конструкция работать не будет.'
            );
        }
    }
}
