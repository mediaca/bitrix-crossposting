<?php

declare(strict_types=1);

use Bitrix\Main\Localization\Loc;

/**
 * @global array $config
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}
?>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_TELEGRAM_ACCESS_TOKEN') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="telegram_access_token" size="40" autocomplete="off"
               value="<?= ($config['telegram']['accessToken'] ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_TELEGRAM_CHAT_USER_NAME') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="telegram_chat_user_name" size="20" autocomplete="off"
               value="<?= ($config['telegram']['chatUserName'] ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_TELEGRAM_MESSAGE_TEMPLATE') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <textarea name="telegram_message_template" autocomplete="off" rows="5"
                  cols="42"><?= htmlspecialchars(array_key_exists('messageTemplate', $config['telegram'] ?? [])
                ? $config['telegram']['messageTemplate']
                : Loc::getMessage('MEDIACA_CROSSPOSTING_TELEGRAM_MESSAGE_TEMPLATE_DEFAULT_VALUE')) ?></textarea>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_TELEGRAM_DATA_PHOTOS') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="telegram_data_photos" size="40" autocomplete="off"
               value="<?= htmlspecialchars(array_key_exists('dataPhotos', $config['telegram'] ?? [])
                   ? implode(',', $config['telegram']['dataPhotos'])
                   : Loc::getMessage('MEDIACA_CROSSPOSTING_TELEGRAM_DATA_PHOTOS_DEFAULT_VALUE')) ?>">
    </td>
</tr>
<tr>
    <td colspan="2">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_TELEGRAM_INSTRUCTION') ?>
    </td>
</tr>
