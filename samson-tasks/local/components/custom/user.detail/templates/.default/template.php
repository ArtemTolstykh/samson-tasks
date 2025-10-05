<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$u = $arResult['USER'];

$fio = trim(($u['LAST_NAME']??'').' '.($u['NAME']??'').' '.($u['SECOND_NAME']??''));

?>

<div class="admin-user-detail" style="max-width:880px;">
    <p><a href="/admin/users/">← К списку</a></p>
    <h1><?= html($u['LOGIN'])?></h1>

    <div>
        <div>
            <div style="padding:12px;border:1px solid #ddd;border-radius:8px;margin-bottom:12px;">
                <div><strong>ID:</strong> <?= (int)$u['ID'] ?></div>
                <div><strong>Логин:</strong> <?= html($u['LOGIN']) ?></div>
                <div><strong>Email:</strong> <?= html($u['EMAIL']) ?></div>
                <div><strong>Активен:</strong> <?= ($u['ACTIVE']==='Y'?'Да':'Нет') ?></div>
            </div>
        </div>
        <div>
            <div style="padding:12px;border:1px solid #ddd;border-radius:8px;margin-bottom:12px;">
                <h3 style="margin-top:0;padding-top:0;">Профиль</h3>
                <div><strong>ФИО:</strong> <?= html($fio) ?></div>
                <div><strong>Телефон (личн.):</strong> <?= html($u['PERSONAL_PHONE']) ?></div>
                <div><strong>Телефон (раб.):</strong> <?= html($u['WORK_PHONE']) ?></div>
                <div><strong>Должность:</strong> <?= html($u['WORK_POSITION']) ?></div>
            </div>
            <div style="padding:12px;border:1px solid #ddd;border-radius:8px;margin-bottom:12px;">
                <h3 style="margin-top:0;padding-top:0;">Системная информация</h3>
                <div><strong>Дата регистрации:</strong> <?= html($u['DATE_REGISTER']) ?></div>
                <div><strong>Последняя авторизация:</strong> <?= html($u['LAST_LOGIN']) ?></div>
                <div><strong>Последнее изменение:</strong> <?= html($u['TIMESTAMP_X']) ?></div>
                <div><strong>Группы:</strong>
                    <?php if(empty($arResult['GROUPS'])): ?>
                        <em>нет</em>
                    <?php else: ?>
                        <ul style="margin:6px 0 0 16px;">
                            <?php foreach($arResult['GROUPS'] as $g): ?>
                                <li><?= html($g['NAME']) ?> (ID: <?= (int)$g['ID'] ?><?= $g['STRING_ID'] ? ', CODE: '.html($g['STRING_ID']) : '' ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
