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

<div class="ev_events">
    <div class="ev_h">
        <h3><?php echo GetMessage('EVENTS')?></h3>
        <a href="#" class="ev_allevents"><?php echo GetMessage('ALL_EVENTS')?></a>
    </div>
    <ul class="ev_lastevent">
        <?foreach($arResult["ITEMS"] as $arItem):?>
        <li>
            <h4><a href=""><?php echo $arItem["NAME"]?></a></h4>
            <p><?php echo $arItem["PREVIEW_TEXT"]?></p>
        </li>
        <?endforeach;?>
    </ul>
    <div class="clearboth"></div>
</div>