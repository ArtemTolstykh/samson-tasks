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
		"CACHE_TYPE" => "A"
	),
	false
);
?>


<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
