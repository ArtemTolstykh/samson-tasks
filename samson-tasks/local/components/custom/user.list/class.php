<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Context;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;

use Local\Traits\ModuleRequireTrait;

class Custom_UserList extends CBitrixComponent
{

    use ModuleRequireTrait;

    /**
     * @throws Throwable
     * @throws LoaderException
     */
    protected function checkModules(): void
    {
        $this->requireModule('main', SystemException::class);
    }

    protected function parseDate(?string $raw, bool $endOfDay = false): ?DateTime
    {
        $raw = $raw !== null ? trim($raw) : '';
        if ($raw === '') {
            return null;
        }

        try {
            $phpDate = new \DateTime($raw);

            $hasTime = (bool)preg_match('/\d{1,2}:\d{2}(:\d{2})?/', $raw);
            if (!$hasTime) {
                $phpDate->setTime($endOfDay ? 23 : 0, $endOfDay ? 59 : 0, $endOfDay ? 59 : 0);
            }

            return new DateTime($phpDate->format('Y-m-d H:i:s'), 'Y-m-d H:i:s');
        } catch (Exception) {
            return null;
        }
    }

    public function executeComponent(): void
    {
        try {
            $this->checkModules();

            $request = Context::getCurrent()->getRequest();

            $emailQuery = trim((string)$request->get('EMAIL'));
            $fromRaw = trim((string)$request->get('LAST_LOGIN_FROM'));
            $toRaw = trim((string)$request->get('LAST_LOGIN_TO'));

            $sortOrder = $this->arParams['SORT_ORDER'];

            $from = $this->parseDate($fromRaw);
            $to = $this->parseDate($toRaw, true);

            if ($from instanceof DateTime && $to instanceof DateTime && $to->getTimestamp() < $from->getTimestamp()) {
                [$from, $to] = [$to, $from];
            }

            $filter = [];

            if ($emailQuery !== '') {
                $filter['%EMAIL'] = $emailQuery;
            }

            if ($from && $to) {
                $filter['><LAST_LOGIN'] = [$from, $to];
            } elseif ($from) {
                $filter['>=LAST_LOGIN'] = $from;
            } elseif ($to) {
                $filter['<=LAST_LOGIN'] = $to;
            }

            $select = [
                'ID',
                'LOGIN',
                'NAME',
                'LAST_NAME',
                'SECOND_NAME',
                'EMAIL',
                'ACTIVE',
                'DATE_REGISTER',
                'LAST_LOGIN'
            ];

            $users = [];
            $res = UserTable::getList([
                'select' => $select,
                'filter' => $filter,
                'order' => ['ID' => $sortOrder],
            ]);

            while ($row = $res->fetch()) {
                $row['DETAIL_URL'] = '/admin/users/' . rawurlencode((string)$row['LOGIN']) . '/';
                if ($row['DATE_REGISTER'] instanceof DateTime) {
                    $row['DATE_REGISTER_FORMATTED'] = $row['DATE_REGISTER']->toString();
                }
                if ($row['LAST_LOGIN'] instanceof DateTime) {
                    $row['LAST_LOGIN_FORMATTED'] = $row['LAST_LOGIN']->toString();
                }
                $users[] = $row;
            }

            $this->arResult = [
                'FILTER' => [
                    'EMAIL' => $emailQuery,
                    'LAST_LOGIN_FROM' => $fromRaw,
                    'LAST_LOGIN_TO' => $toRaw,
                ],
                'ITEMS' => $users,
            ];

            $this->includeComponentTemplate();
        } catch (Throwable $e) {
            ShowError($e->getMessage());
        }
    }
}
