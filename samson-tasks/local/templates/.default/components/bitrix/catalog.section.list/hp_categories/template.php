<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

<div class="cn_hp_category">
    <ul>
        <?foreach($arResult["SECTIONS"] as $arSection):?>
        <?php if(empty($arSection['PICTURE']['SRC']) && empty($arSection['DESCRIPTION'])) continue?>
        <li>
            <img src="<?php echo $arSection['PICTURE']['SRC']?>" alt="<?php echo $arSection['PICTURE']['ALT']?>">
            <h2><a href="<?php echo $arSection['SECTION_PAGE_URL'];?>"><?php echo $arSection['NAME'];?></a></h2>
            <p><?php echo $arSection['DESCRIPTION'];?> <a class="cn_hp_categorymore" href="/catalog/">&rarr;</a></p>
            <div class="clearboth"></div>
        </li>
        <?endforeach;?>
    </ul>
    <a href="/catalog/" class="cn_hp_category_more">Все разделы каталога &rarr;</a>
</div>
