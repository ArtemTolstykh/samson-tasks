<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Iblock\Component\Tools as IblockTools;

global $USER;

if (!Loader::includeModule('iblock')) return;

$arParams['IBLOCK_ID'] = (int)($arParams['IBLOCK_ID'] ?? 0);
$arParams['ELEMENT_ID'] = (int)($arParams['ELEMENT_ID'] ?? 0);
$arParams['SIMILAR_LIMIT'] = max(1, (int)($arParams['SIMILAR_LIMIT'] ?? 5));
$arParams['MIN_WORD_LEN'] = max(1, (int)($arParams['MIN_WORD_LEN'] ?? 4));

if ($arParams['IBLOCK_ID'] <= 0 || $arParams['ELEMENT_ID'] <= 0) {
    IblockTools::process404('Element not found', true, true, true);
    return;
}

$cacheId = ['v1', $arParams['IBLOCK_ID'], $arParams['ELEMENT_ID'], $USER->getGroups()];
if($this->StartResultCache(false, $cacheId)) {
    $select = [
        'ID','IBLOCK_ID','NAME','DETAIL_TEXT','DETAIL_TEXT_TYPE',
        'DETAIL_PICTURE','PREVIEW_TEXT','PREVIEW_TEXT_TYPE','PREVIEW_PICTURE',
        'ACTIVE','ACTIVE_FROM','DATE_CREATE'
    ];

    $filter = [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ID' => $arParams['ELEMENT_ID'],
        'ACTIVE' => 'Y',
    ];

    $res = CIBlockElement::GetList([], $filter, false, false, $select);

    if(!($item = $res->GetNext())) {
        $this->AbortResultCache();
        IblockTools::process404('Element not found', true, true, true);
        return;
    }

    $item['DETAIL_PICTURE'] = $item['DETAIL_PICTURE'] ? CFile::GetFileArray($item['DETAIL_PICTURE']) : null;
    if(!$item['DETAIL_PICTURE'] && $item['PREVIEW_PICTURE']){
        $item['DETAIL_PICTURE'] = CFile::GetFileArray($item['PREVIEW_PICTURE']);
    }

    $props = [];
    $propRes = CIBlockElement::GetProperty($item['IBLOCK_ID'], $item['ID'], ['sort' => 'asc'], []);
    while ($p = $propRes->Fetch()) {
        $code = $p['CODE'] ?: $p['ID'];
        if ($p['MULTIPLE'] === 'Y') {
            $props[$code]['NAME'] = $p['NAME'];
            $props[$code]['VALUES'][] = $p['VALUE'];
        } else {
            $props[$code] = ['NAME'=>$p['NAME'],'VALUE'=>$p['VALUE']];
        }
    }

    $name = (string)$item['~NAME'];
    $name = mb_strtolower($name);
    $tokens = preg_split('/[^\p{L}\p{N}]+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
    $minLen = $arParams['MIN_WORD_LEN'];
    $stop = [
        'это','для','или','если','когда','такой','такая','такие','также',
        'после','перед','через','между','над','под','всегда','данный','этот',
        'этом','эти','этим','этой','того','чтобы','более','менее','свой','ваш',
    ];

    $dict = [];
    foreach ($tokens as $w) {
        if (mb_strlen($w) >= $minLen && !in_array($w, $stop, true)) {
            $dict[$w] = true;
        }
    }
    $words = array_keys($dict);

    $similar = [];
    if(!empty($words)) {
        $or = ['LOGIC' => 'OR'];

        foreach ($words as $w) {
            $or[] = ['%NAME'=>$w];
        }

        $simFilter = [
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'ACTIVE' => 'Y',
            '!ID' => $item['ID'],
            $or,
        ];

        $simSel = ['ID','NAME','ACTIVE_FROM'];

        $simRes = CIBlockElement::GetList(
            ['ACTIVE_FROM' => 'DESC'],
            $simFilter,
            false,
            ['nTopCount'=>$arParams['SIMILAR_LIMIT']],
            $simSel,
        );

        while ($r = $simRes->GetNext()) {
            $similar[] = [
                'ID'   => (int)$r['ID'],
                'NAME' => $r['NAME'],
                'URL'  => '/admin/iblock/detail.php?ID='.$r['ID'],
            ];
        }
    }

    $arResult = [
        'ITEM' => [
            'ID'               => (int)$item['ID'],
            'NAME'             => $item['NAME'],
            'DETAIL_TEXT'      => $item['DETAIL_TEXT'],
            'DETAIL_TEXT_TYPE' => $item['DETAIL_TEXT_TYPE'],
            'PICTURE'          => $item['DETAIL_PICTURE'],
            'PROPERTIES'       => $props,
            'ACTIVE_FROM'      => $item['ACTIVE_FROM'],
            'DATE_CREATE'      => $item['DATE_CREATE'],
        ],
        'SIMILAR' => $similar,
    ];

    $this->IncludeComponentTemplate();

    if ($arParams['SET_TITLE'] == 'Y') {
        global $APPLICATION;
        $APPLICATION->SetTitle($arResult['ITEM']['NAME']);
    }
}