<?php

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

if (file_exists($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/functions.php')) {
    require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/functions.php');
}

try {
    Loader::registerAutoLoadClasses(null, [
        'Local\Traits\ModuleRequireTrait' => '/local/lib/Traits/ModuleRequireTrait.php',
    ]);
} catch (LoaderException $e) {
    $e-getMessage('Не удалось загрузить трейт', $e);
}

