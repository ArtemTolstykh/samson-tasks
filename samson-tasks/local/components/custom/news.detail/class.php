<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Iblock\Component\Tools as IblockTools;
use Bitrix\Main\LoaderException;

use Local\Traits\ModuleRequireTrait;

class Custom_NewsDetail extends CBitrixComponent
{
    use ModuleRequireTrait;

    /**
     * @throws LoaderException
     * @throws Throwable
     */
    protected function checkModules(): void
    {
        $this->requireModule('iblock');
    }

    public function onPrepareComponentParams($arParams)
    {
        if (!is_array($arParams)) {
            $arParams = [];
        }

        $arParams['IBLOCK_ID'] = (int)($arParams['IBLOCK_ID'] ?? 0);
        $arParams['ELEMENT_ID'] = (int)($arParams['ELEMENT_ID'] ?? 0);
        $arParams['SIMILAR_LIMIT'] = max(1, (int)($arParams['SIMILAR_LIMIT'] ?? 5));
        $arParams['MIN_WORD_LEN'] = max(1, (int)($arParams['MIN_WORD_LEN'] ?? 4));

        $raw = (string)($arParams['SET_STOP_WORDS'] ?? '');
        $arParams['SET_STOP_WORDS'] = array_filter(array_map('mb_strtolower', explode(' ', $raw)));

        return $arParams;
    }

    protected function getUserGroups(): array
    {
        global $USER;
        return is_object($USER) ? (array)$USER->GetGroups() : [];
    }

    protected function loadElement(array $select, array $filter): ?array
    {
        $res = CIBlockElement::GetList([], $filter, false, false, $select);
        $item = $res->GetNext();

        if (!$item) {
            return null;
        }

        $item['DETAIL_PICTURE'] = $item['DETAIL_PICTURE'] ? CFile::GetFileArray($item['DETAIL_PICTURE']) : null;
        if (!$item['DETAIL_PICTURE'] && $item['PREVIEW_PICTURE']) {
            $item['DETAIL_PICTURE'] = CFile::GetFileArray($item['PREVIEW_PICTURE']);
        }

        return $item;
    }

    protected function loadProperties(int $iblockId, int $elementId): array
    {
        $props = [];
        $propRes = CIBlockElement::GetProperty($iblockId, $elementId, ['sort' => 'asc'], []);

        while ($p = $propRes->Fetch()) {
            $code = $p['CODE'] ?: $p['ID'];
            if ($p['MULTIPLE'] === 'Y') {
                $props[$code]['NAME'] = $p['NAME'];
                $props[$code]['VALUES'][] = $p['VALUE'];
            } else {
                $props[$code] = [
                    'NAME' => $p['NAME'],
                    'VALUE' => $p['VALUE']
                ];
            }
        }
        return $props;
    }

    protected function tokenizeName(string $name, int $minLen, array $stop): array
    {
        $name = mb_strtolower($name);
        $tokens = preg_split('/[^\p{L}\p{N}]+/u', $name, -1, PREG_SPLIT_NO_EMPTY);

        $dict = [];

        foreach ($tokens as $word) {
            if (mb_strlen($word) >= $minLen && !in_array($word, $stop, true)) {
                $dict[$word] = true;
            }
        }

        return array_keys($dict);
    }

    protected function buildSimilar(int $iblockId, int $excludeId, array $words, int $limit): array
    {
        if (empty($words)) {
            return [];
        }

        $or = ['LOGIC' => 'OR'];
        foreach ($words as $word) {
            $or[] = ['%NAME' => $word];
        }

        $simFilter = [
            'IBLOCK_ID' => $iblockId,
            'ACTIVE' => 'Y',
            '!ID' => $excludeId,
            $or,
        ];

        $simRes = CIBlockElement::GetList(
            ['ACTIVE_FROM' => 'DESC'],
            $simFilter,
            false,
            ['nTopCount' => $limit],
            ['ID', 'NAME', 'ACTIVE_FROM']
        );

        $similar = [];
        while ($res = $simRes->GetNext()) {
            $similar[] = [
                'ID' => (int)$res['ID'],
                'NAME' => $res['NAME'],
                'URL' => '/admin/iblock/detail.php?ID=' . (int)$res['ID'],
            ];
        }
        return $similar;
    }

    public function executeComponent(): void
    {
        try {
            $this->checkModules();

            if ($this->arParams['IBLOCK_ID'] <= 0 || $this->arParams['ELEMENT_ID'] <= 0) {
                IblockTools::process404('Элемент не найден', true, true, true);
                return;
            }

            $cacheId = ['v1', $this->arParams['IBLOCK_ID'], $this->arParams['ELEMENT_ID'], $this->getUserGroups()];

            if ($this->startResultCache(false, $cacheId)) {
                $select = [
                    'ID','IBLOCK_ID','NAME','DETAIL_TEXT','DETAIL_TEXT_TYPE',
                    'DETAIL_PICTURE','PREVIEW_TEXT','PREVIEW_TEXT_TYPE','PREVIEW_PICTURE',
                    'ACTIVE','ACTIVE_FROM','DATE_CREATE'
                ];

                $filter = [
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    'ID' => $this->arParams['ELEMENT_ID'],
                    'ACTIVE' => 'Y',
                ];

                $item = $this->loadElement($select, $filter);
                if (!$item) {
                    $this->abortResultCache();
                    IblockTools::process404('Элемент не найден', true, true, true);
                    return;
                }

                $props = $this->loadProperties((int)$item['IBLOCK_ID'], (int)$item['ID']);

                $words   = $this->tokenizeName((string)$item['~NAME'], (int)$this->arParams['MIN_WORD_LEN'], (array)$this->arParams['SET_STOP_WORDS']);

                $similar = $this->buildSimilar((int)$item['IBLOCK_ID'], (int)$item['ID'], $words, (int)$this->arParams['SIMILAR_LIMIT']);

                $this->arResult = [
                    'ITEM' => [
                        'ID' => (int)$item['ID'],
                        'NAME' => $item['NAME'],
                        'DETAIL_TEXT' => $item['DETAIL_TEXT'],
                        'DETAIL_TEXT_TYPE' => $item['DETAIL_TEXT_TYPE'],
                        'PICTURE' => $item['DETAIL_PICTURE'],
                        'PROPERTIES' => $props,
                        'ACTIVE_FROM' => $item['ACTIVE_FROM'],
                        'DATE_CREATE' => $item['DATE_CREATE'],
                    ],
                    'SIMILAR' => $similar,
                ];

                $this->includeComponentTemplate();
            }

            if ($this->arParams['SET_TITLE'] === 'Y' && !empty($this->arResult['ITEM']['NAME'])) {
                global $APPLICATION;
                $APPLICATION->SetTitle($this->arResult['ITEM']['NAME']);
            }

        } catch (Throwable $e) {
            $this->abortResultCache();
            ShowError($e->getMessage());
        }
    }
}