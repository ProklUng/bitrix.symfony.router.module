<?php

use ProklUng\Module\Boilerplate\Module;

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

// Странный эффект: на новой версии Битрикса на этой стадии класс модуля не инстанцирован.
// На старом - все OK.
try {
    $module = Module::getModuleInstance('proklung.symfony.router');
} catch (\LogicException $e) {
    new proklung_symfony_router();
    $module = Module::getModuleInstance('proklung.symfony.router');
}

$module->showOptionsForm();

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
