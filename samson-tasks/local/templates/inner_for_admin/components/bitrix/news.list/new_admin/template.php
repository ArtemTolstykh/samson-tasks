<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);

// Подключаем JS
Asset::getInstance()->addJs($this->GetFolder().'/js/seeMoreButton.js');

/**
 * @param array $item
 * @param int $limit
 * @return array
 *
 * Возвращает [teaser, hasMore, fullHtml, isHtml]
 * $limit — кол-во слов в тизере
 */
function buildTeaser(array $item, int $limit = 10): array
{
    $isHtml = ($item['DETAIL_TEXT_TYPE'] ?? $item['PREVIEW_TEXT_TYPE'] ?? 'text') === 'html';
    $fullHtml = $item['DETAIL_TEXT'] ?: $item['PREVIEW_TEXT'] ?: '';

    $plain = trim(preg_replace('/\s+/u', ' ', strip_tags($fullHtml)));
    $words = preg_split('/\s+/u', $plain, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $hasMore = count($words) > $limit;

    $teaserText = implode(' ', array_slice($words, 0, $limit));
    if ($teaserText === '' && $fullHtml !== '') {
        $teaserText = mb_substr($plain, 0, 120);
    }

    $teaser = htmlspecialchars($teaserText, ENT_QUOTES | ENT_SUBSTITUTE);

    if (!$isHtml) {
        $fullHtml = nl2br(htmlspecialchars($fullHtml, ENT_QUOTES | ENT_SUBSTITUTE));
    }

    return [$teaser, $hasMore, $fullHtml, $isHtml];
}
?>

<?php foreach ($arResult["ITEMS"] as $arItem): ?>
    <?php
    $id = (int)$arItem['ID'];
    [$teaser, $hasMore, $fullHtml] = buildTeaser($arItem, 10);
    ?>
    <div class="news-item" id="news-<?= $id ?>">
        <div class="ps_head">
            <a class="ps_head_link" href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                <h2 class="ps_head_h"><?= htmlspecialchars($arItem["NAME"]) ?></h2>
            </a>
            <span class="ps_date"><?= $arItem["DISPLAY_ACTIVE_FROM"] ?></span>
        </div>

        <div class="ps_content">
            <?php if (!empty($arItem["PREVIEW_PICTURE"]["SRC"])): ?>
                <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" class="news-thumb" alt="<?= htmlspecialchars($arItem["NAME"]) ?>">
            <?php endif; ?>

            <p class="news-teaser" id="teaser-<?= $id ?>">
                <?= $teaser ?><?php if ($hasMore): ?>…<?php endif; ?>
            </p>

            <?php if ($hasMore): ?>
                <button
                        class="js-toggle-full"
                        type="button"
                        data-target="<?= $id ?>"
                        data-label-open="показать все"
                        data-label-close="скрыть все"
                        aria-controls="full-<?= $id ?>"
                        aria-expanded="false"
                >
                    показать все
                </button>
            <?php endif; ?>

            <div class="news-full" id="full-<?= $id ?>" hidden>
                <?= $fullHtml ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<style>
    .news-thumb { float: left; margin: 0 16px 8px 0; max-width: 240px; height: auto; }
    .news-full[hidden] { display: none !important; }
    .js-toggle-full { margin: 8px 0; }
</style>
