<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle('Детальная новость');
?>

<?php
$APPLICATION->IncludeComponent(
	"custom:news.detail", 
	".default", 
	array(
		"IBLOCK_ID" => "1",
		"ELEMENT_ID" => (int)($_GET["ID"]??0),
		"SIMILAR_LIMIT" => "5",
		"MIN_WORD_LEN" => "4",
		"CACHE_TIME" => "3600",
		"SET_TITLE" => "Y",
		"ADD_CHAIN_ITEM" => "N",
		"COMPONENT_TEMPLATE" => ".default",
		"CACHE_TYPE" => "A",
		"SET_STOP_WORDS" => "это для или если когда такой такая такие также после перед через между над под всегда данный этот этом эти этим этой того чтобы более менее свой ваш"
	),
	false
);
?>


<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
