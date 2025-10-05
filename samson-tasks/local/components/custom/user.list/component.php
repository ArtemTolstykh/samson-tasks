<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Context;

if (!Loader::includeModule('main')) return;

$request = Context::getCurrent()->getRequest();
$emailQuery = trim((string)$request->get('EMAIL'));
$fromRaw = trim((string)$request->get('LAST_LOGIN_FROM'));
$toRaw = trim((string)$request->get('LAST_LOGIN_TO'));

function normalizeDateForCUser(string $raw = null, bool $endOfDay = false): ?string
{
    if (!$raw) return null;
    $raw = trim($raw);

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
        $fmt = 'Y-m-d';
    } elseif (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $raw)) {
        $fmt = 'd.m.Y';
    } else {
        return null;
    }

    $time = $endOfDay ? '23:59:59' : '00:00:00';
    $dt = \DateTime::createFromFormat($fmt.' H:i:s', $raw.' '.$time)
        ?: \DateTime::createFromFormat($fmt, $raw);

    return $dt ? $dt->format('d.m.Y H:i:s') : null;
}

$filter = [];

if ($emailQuery !== '') {
    $filter['EMAIL'] = $emailQuery;
}

if ($from = normalizeDateForCUser($fromRaw, false)) {
    $filter['LAST_LOGIN_1'] = $from;
}
if ($to = normalizeDateForCUser($toRaw, true)) {
    $filter['LAST_LOGIN_2'] = $to;
}

$by = 'ID';
$order = 'ASC';

$select = [
    'ID','LOGIN','NAME','LAST_NAME','SECOND_NAME','EMAIL',
    'ACTIVE','DATE_REGISTER','LAST_LOGIN'
];

$users = [];
$rsUsers = CUser::GetList($by, $order, $filter, ['FIELDS' => $select]);

while ($u = $rsUsers->Fetch()) {
    $u['DETAIL_URL'] = '/admin/users/'.rawurlencode($u['LOGIN']).'/';
    $users[] = $u;
}

$arResult = [
    'FILTER' => [
        'EMAIL' => $emailQuery,
        'LAST_LOGIN_FROM' => $fromRaw,
        'LAST_LOGIN_TO'   => $toRaw,
    ],
    'ITEMS' => $users,
];

$this->IncludeComponentTemplate();
