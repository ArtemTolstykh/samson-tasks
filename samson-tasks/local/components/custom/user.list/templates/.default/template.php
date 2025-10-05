<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$F = $arResult['FILTER'];

?>


<div class="admin-users-list">
    <h1>Пользователи</h1>

    <form method="get" class="admin-users-list__filter" style="margin:16px 0;padding:12px;border:1px solid #ddd;border-radius:8px;">
        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <label>Email (подстрока)</label><br>
                <input type="text" name="EMAIL" value="<?=html($F['EMAIL'])?>" style="padding:6px 8px;min-width:220px;">
            </div>
            <div>
                <label>Последняя авторизация с</label><br>
                <input type="date" name="LAST_LOGIN_FROM" value="<?=html($F['LAST_LOGIN_FROM'])?>" style="padding:6px 8px;">
            </div>
            <div>
                <label>по</label><br>
                <input type="date" name="LAST_LOGIN_TO" value="<?=html($F['LAST_LOGIN_TO'])?>" style="padding:6px 8px;">
            </div>
            <div>
                <button type="submit" style="padding:8px 14px;">Применить</button>
                <a href="/admin/users/" style="padding:8px 14px;display:inline-block;">Сбросить</a>
            </div>
        </div>
    </form>

    <div class="admin-users-list__table" style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
            <tr>
                <th style="border-bottom:1px solid #ddd;padding:8px;text-align:left;">ID</th>
                <th style="border-bottom:1px solid #ddd;padding:8px;text-align:left;">Логин</th>
                <th style="border-bottom:1px solid #ddd;padding:8px;text-align:left;">ФИО</th>
                <th style="border-bottom:1px solid #ddd;padding:8px;text-align:left;">Email</th>
                <th style="border-bottom:1px solid #ddd;padding:8px;text-align:left;">Активен</th>
                <th style="border-bottom:1px solid #ddd;padding:8px;text-align:left;">Дата регистрации</th>
                <th style="border-bottom:1px solid #ddd;padding:8px;text-align:left;">Последняя авторизация</th>
            </tr>
            </thead>
            <tbody>
            <?php if(empty($arResult['ITEMS'])): ?>
                <tr><td colspan="7" style="padding:12px;">Ничего не найдено</td></tr>
            <?php else: foreach($arResult['ITEMS'] as $u):
                $fio = trim($u['LAST_NAME'].' '.$u['NAME'].' '.$u['SECOND_NAME']);
                ?>
                <tr>
                    <td style="border-bottom:1px solid #f0f0f0;padding:8px;"><?= (int)$u['ID'] ?></td>
                    <td style="border-bottom:1px solid #f0f0f0;padding:8px;">
                        <a href="<?=html($u['DETAIL_URL'])?>"><?=html($u['LOGIN'])?></a>
                    </td>
                    <td style="border-bottom:1px solid #f0f0f0;padding:8px;"><?=html($fio)?></td>
                    <td style="border-bottom:1px solid #f0f0f0;padding:8px;"><?=html($u['EMAIL'])?></td>
                    <td style="border-bottom:1px solid #f0f0f0;padding:8px;"><?= ($u['ACTIVE']==='Y'?'Да':'Нет') ?></td>
                    <td style="border-bottom:1px solid #f0f0f0;padding:8px;"><?=html($u['DATE_REGISTER'])?></td>
                    <td style="border-bottom:1px solid #f0f0f0;padding:8px;"><?=html($u['LAST_LOGIN'])?></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
