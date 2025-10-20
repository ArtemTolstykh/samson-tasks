<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Iblock\Component\Tools;
use Bitrix\Main\LoaderException;

use Local\Traits\ModuleRequireTrait;

class Custom_UserDetail extends CBitrixComponent
{
    use ModuleRequireTrait;

    protected bool $hasIblockTools = false;

    /**
     * @throws Throwable
     * @throws LoaderException
     */
    protected function checkModules(): void
    {
        $this->requireModule('main');
    }

    public function onPrepareComponentParams($arParams)
    {
        if (!is_array($arParams)) {
            $arParams = [];
        }

        $arParams['LOGIN'] = trim((string)($arParams['LOGIN'] ?? ''));
        $arParams['BACK_URL'] = (string)($arParams['BACK_URL'] ?? '/admin/users/');
        $arParams['CACHE_TIME'] = (int)($arParams['CACHE_TIME'] ?? 3600);

        $arParams['SET_TITLE'] = ((string)($arParams['SET_TITLE'] ?? 'Y') === 'Y') ? 'Y' : 'N';

        return $arParams;
    }

    protected function notFound(): void
    {
        @define('ERROR_404', 'Y');
        if ($this->hasIblockTools && class_exists('\Bitrix\Iblock\Component\Tools')) {
            Tools::process404('Пользователь не найден', true, true, true);
        } else {
            CHTTP::SetStatus('404 Not Found');
            ShowError('Пользователь не найден');
        }
    }

    protected function loadUserByLogin(string $login): ?array
    {
        if ($login === '') return null;

        $by = 'ID';
        $order = 'ASC';

        $filter = ['LOGIN' => $login];

        $res = CUser::GetList($by, $order, $filter, [
            'FIELDS' => [
                'ID',
                'LOGIN',
                'NAME',
                'LAST_NAME',
                'SECOND_NAME',
                'EMAIL',
                'ACTIVE',
                'DATE_REGISTER',
                'LAST_LOGIN',
                'TIMESTAMP_X',
                'PERSONAL_PHONE',
                'WORK_PHONE',
                'PERSONAL_MOBILE',
                'WORK_POSITION',
            ],
        ]);

        $user = $res ? $res->Fetch() : false;
        return $user ?: null;
    }

    protected function loadGroupsById(int $userId): array
    {
        $groupIds = CUser::GetUserGroup($userId);
        if (empty($groupIds)) return [];

        $by = 'C_SORT';
        $order = 'ASC';

        $groups = [];

        $resGroups = CGroup::GetList($by, $order, ['ID' => implode('|', array_map('intval', $groupIds))]);

        while ($group = $resGroups->Fetch()) {
            $groups[] = [
                'ID' => (int)$group['ID'],
                'NAME' => $group['NAME'],
                'STRING_ID' => $group['STRING_ID'],
            ];
        }

        return $groups;
    }

    public function executeComponent(): void
    {
        try {
            $this->checkModules();

            if ($this->arParams['LOGIN'] === '') {
                $this->notFound();
                return;
            }

            $cacheID = ['v1', $this->arParams['LOGIN']];
            if ($this->startResultCache(false, $cacheID)) {
                $user = $this->loadUserByLogin($this->arParams['LOGIN']);
                if (!$user) {
                    $this->abortResultCache();
                    $this->notFound();
                    return;
                }

                $groups = $this->loadGroupsById((int)$user['ID']);

                $this->arResult = [
                    'USER' => $user,
                    'GROUPS' => $groups,
                    'BACK_URL' => $this->arParams['BACK_URL'],
                ];

                $this->includeComponentTemplate();
            }

            if ($this->arParams['SET_TITLE'] === 'Y' && !empty($this->arResult['USER'])) {
                $user = $this->arResult['USER'];
                $title = trim(($user['LAST_NAME'] ?? '').' '.($user['NAME'] ?? '').' '.($user['SECOND_NAME'] ?? ''));
                if ($title === '') {
                    $title = $user['LOGIN'];
                }
                global $APPLICATION;
                $APPLICATION->SetTitle($title);
            }
        } catch (Throwable $e) {
            $this->abortResultCache();
            ShowError($e->getMessage());
        }
    }
}