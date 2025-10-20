<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = [
    'PARAMETERS' => [

        'BACK_URL' => [
            'PARENT' => 'BASE',
            'NAME' => 'Ссылка "Назад к списку"',
            'TYPE' => 'STRING',
            'DEFAULT' => '/admin/users/',
        ],

        'CACHE_TIME' => [
            'PARENT' => 'CACHE_SETTINGS',
            'NAME' => 'Время кеширования (сек.)',
            'TYPE' => 'STRING',
            'DEFAULT' => 0,
        ],

        'SET_TITLE' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => 'Устанавливать заголовок страницы',
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ],

        'LOGIN' => [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => 'LOGIN пользователя (для проверки шаблона)',
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ],
    ],
];
