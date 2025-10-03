<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = [
    'PARAMETERS' => [
        'IBLOCK_ID' => [
            'PARENT'=>'BASE','NAME'=>'ID инфоблока','TYPE'=>'STRING'
        ],
        'ELEMENT_ID' => [
            'PARENT'=>'BASE','NAME'=>'ID элемента','TYPE'=>'STRING'
        ],
        'SIMILAR_LIMIT' => [
            'PARENT'=>'ADDITIONAL_SETTINGS','NAME'=>'Лимит похожих','TYPE'=>'STRING','DEFAULT'=>'5'
        ],
        'MIN_WORD_LEN' => [
            'PARENT'=>'ADDITIONAL_SETTINGS','NAME'=>'Мин. длина слова','TYPE'=>'STRING','DEFAULT'=>'4'
        ],
        'CACHE_TIME' => ['DEFAULT'=>3600],
    ],
];
