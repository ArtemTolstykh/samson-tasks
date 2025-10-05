<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;

if(!Loader::includeModule('main')) return;

$login = trim((string)($arParams['LOGIN'] ?? ''));
if($login === ''){
    @define('ERROR_404','Y');
    \Bitrix\Iblock\Component\Tools::process404('Пользователь не найден', true, true, true);
    return;
}

$rs = CUser::GetList($by='ID', $order='ASC', ['LOGIN' => $login], [
    'FIELDS'=>[
        'ID','LOGIN','NAME','LAST_NAME','SECOND_NAME','EMAIL',
        'ACTIVE','DATE_REGISTER','LAST_LOGIN','TIMESTAMP_X',
        'PERSONAL_PHONE','WORK_PHONE','PERSONAL_MOBILE','WORK_POSITION',
    ],
]);

$user = $rs->Fetch();
if(!$user){
    @define('ERROR_404','Y');
    \Bitrix\Iblock\Component\Tools::process404('Пользователь не найден', true, true, true);
    return;
}

$groupIds = CUser::GetUserGroup($user['ID']);
$groups = [];
if(!empty($groupIds)){
    $rsG = CGroup::GetList($by='C_SORT', $order='ASC', ['ID' => implode('|', array_map('intval', $groupIds))]);
    while($g = $rsG->Fetch()){
        $groups[] = [
            'ID' => (int)$g['ID'],
            'NAME' => $g['NAME'],
            'STRING_ID' => $g['STRING_ID'],
        ];
    }
}

$arResult = [
    'USER'   => $user,
    'GROUPS' => $groups,
    'BACK_URL' => '/admin/users/',
];

$this->IncludeComponentTemplate();
