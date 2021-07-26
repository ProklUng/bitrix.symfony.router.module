<?php

namespace Proklung\Symfony\Router\Subscribers;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Proklung\Symfony\Router\Utils\OptionsManager;

/**
 * Class OnAfterSaveOptionsHandler
 * @package Proklung\Symfony\Router\Subscribers
 *
 * @since 25.07.2021
 */
class OnAfterSaveOptionsHandler
{
    /**
     * Обработчик события "после сохранения опций" модуля.
     *
     * @return void
     * @throws ArgumentNullException | ArgumentOutOfRangeException
     */
    public function handler() : void
    {
        if (OptionsManager::option('php_router_config_path')
            &&
            OptionsManager::option('native_config_file_path')
        ) {
            if (!@file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/routes/')) {
                @mkdir($_SERVER['DOCUMENT_ROOT'] . '/local/routes/', 0777);
            }

            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/local/routes/' . OptionsManager::option('php_router_config_path');
            if (@file_exists($filePath)
                && !$this->isFilesAreEqual($filePath, __DIR__ . '/../../configs/route_config_template.php.tmpl')) {
                @unlink($filePath . '.backup');
                copy($filePath, $filePath . '.backup');
                copy(
                    __DIR__ . '/../../configs/route_config_template.php.tmpl',
                    $filePath
                );
            }

            if (!@file_exists($filePath)) {
                copy(
                    __DIR__ . '/../../configs/route_config_template.php.tmpl',
                    $filePath
                );
            }
        }
    }

    /**
     * Сравнение файлов на идентичность.
     *
     * @param string $fileOne Файл 1.
     * @param string $fileTwo Файл 2.
     *
     * @return boolean
     */
    private function isFilesAreEqual(string $fileOne, string $fileTwo) : bool
    {
        if (filesize($fileOne) !== filesize($fileTwo)) {
            return false;
        }

        $ah = fopen($fileOne, 'rb');
        $bh = fopen($fileTwo, 'rb');

        $result = true;
        while (!feof($ah)) {
            if (fread($ah, 8192) != fread($bh, 8192)) {
                $result = false;
                break;
            }
        }

        fclose($ah);
        fclose($bh);

        return $result;
    }
}
