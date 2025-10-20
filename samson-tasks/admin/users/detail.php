<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("detail");
?>

<?php
$APPLICATION->IncludeComponent(
	"custom:user.detail", 
	".default", 
	array(
		"LOGIN" => isset($_GET["LOGIN"])?rawurldecode($_GET["LOGIN"]):"",
		"CACHE_TIME" => "0",
		"COMPONENT_TEMPLATE" => ".default",
		"BACK_URL" => "/admin/users/",
		"CACHE_TYPE" => "N",
		"SET_TITLE" => "Y"
	),
	false
);?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>