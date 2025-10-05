<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("detail");
?>

<?php
$APPLICATION->IncludeComponent('custom:user.detail', '', [
    'LOGIN' => isset($_GET['LOGIN']) ? rawurldecode($_GET['LOGIN']) : '',
    'CACHE_TIME' => 0,
], false)
?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>