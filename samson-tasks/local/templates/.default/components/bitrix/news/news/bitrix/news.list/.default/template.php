<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<?php foreach($arResult["ITEMS"] as $arItem):?>
    <div class="ps_head"><a class="ps_head_link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><h2 class="ps_head_h"><?php echo $arItem["NAME"]?></h2></a><span class="ps_date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></span></div>
    <div class="ps_content">
        <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?php echo $arItem["NAME"]?>">
        <?php echo $arItem["PREVIEW_TEXT"];?>
        <div class="clear:both"></div>
    </div>
<?php endforeach;?>
