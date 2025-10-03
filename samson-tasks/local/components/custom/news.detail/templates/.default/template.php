<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$item = $arResult['ITEM'];
?>

<div class="ps_content">
    <h1><?=$item['NAME']?></h1>

    <?php if(!empty($item['PICTURE']['SRC'])): ?>
        <div class="admin-news-detail__image">
            <img src="<?=$item['PICTURE']['SRC']?>" alt="<?=htmlspecialcharsbx($item['NAME'])?>">
        </div>
    <?php endif; ?>

    <div class="admin-news-detail__text">
        <?php if($item['DETAIL_TEXT_TYPE']==='html'): ?>
            <?=$item['DETAIL_TEXT']?>
        <?php else: ?>
            <p><?=nl2br(htmlspecialcharsbx($item['DETAIL_TEXT']))?></p>
        <?php endif; ?>
    </div>

    <?php if(!empty($item['PROPERTIES'])): ?>
        <div class="admin-news-detail__props">
            <h3>Свойства</h3>
            <dl>
                <?php foreach($item['PROPERTIES'] as $code=>$prop):
                    $title = $prop['NAME'];
                    $val   = isset($prop['VALUES']) ? $prop['VALUES'] : $prop['VALUE'];
                    if(is_array($val)) $val = implode(', ', array_filter(array_map('strval',$val)));
                    if($val === '' || $val === null) continue;
                    ?>
                    <dt><?=htmlspecialcharsbx($title)?></dt>
                    <dd><?=htmlspecialcharsbx($val)?></dd>
                <?php endforeach; ?>
            </dl>
        </div>
    <?php endif; ?>

    <div class="admin-news-detail__similar">
        <h3>Похожие новости</h3>
        <?php if(!empty($arResult['SIMILAR'])): ?>
            <ul>
                <?php foreach($arResult['SIMILAR'] as $s): ?>
                    <li><a href="<?=$s['URL']?>"><?=htmlspecialcharsbx($s['NAME'])?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Похожие новости не найдены.</p>
        <?php endif; ?>
    </div>

    <a href="/admin/iblock/">К списку новостей</a>
</div>