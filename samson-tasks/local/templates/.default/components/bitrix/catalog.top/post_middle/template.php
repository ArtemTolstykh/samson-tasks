<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogTopComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);

function enterTitle(array $arItem): void
{
    if ($arItem['NAME'] == 'Новинки') {
        echo 'cn_hp_post_new';
    } elseif ($arItem['NAME'] == 'Акции') {
        echo 'cn_hp_post_action';
    } elseif ($arItem['NAME'] == 'Хиты продаж') {
        echo 'cn_hp_post_bestsellersn';
    }
}
?>

<div class="cn_hp_post">
    <?foreach($arResult["ITEMS"] as $arItem):?>
    <div class="<?php enterTitle($arItem)?>">
        <h3><?php echo $arItem['NAME'];?></h3>
        <img src="<?php echo $arItem['PREVIEW_PICTURE']['SRC'];?>" alt="">
        <p><?php echo $arItem['PREVIEW_TEXT'];?></p>
        <div class="clearboth"></div>
    </div>
    <?endforeach;?>
</div>
