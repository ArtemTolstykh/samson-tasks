<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Пользователи на сайте");
?>

<?php
    $APPLICATION->IncludeComponent('custom:user.list', '', [
        'PAGE_SIZE' => '10',
        'CACHE_TIME' => '0',
    ], false);
?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>