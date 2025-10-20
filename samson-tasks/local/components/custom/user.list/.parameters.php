<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = [
    'PARAMETERS' => [
        'SORT_ORDER' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => 'Направление сортировки',
            'TYPE' => 'LIST',
            'VALUES' => ['ASC' => 'ASC', 'DESC' => 'DESC'],
            'DEFAULT' => 'ASC',
        ],
    ],
];