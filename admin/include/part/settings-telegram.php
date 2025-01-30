<?php

declare(strict_types=1);


use Bitrix\Main\Localization\Loc;

/**
 * @global array $settings
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}
?>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_TELEGRAM_ACCESS_TOKEN') ?>:
    </td>
    <td width="50%" class="adm-detail-content-cell-r">
        <input type="text" name="telegram_access_token" size="40" autocomplete="off"
               value="<?= ($settings['telegram']['accessToken'] ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_TELEGRAM_CHAT_USER_NAME') ?>:
    </td>
    <td width="50%" class="adm-detail-content-cell-r">
        <input type="text" name="telegram_chat_user_name" size="20" autocomplete="off"
               value="<?= ($settings['telegram']['chatUserName'] ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_TELEGRAM_MESSAGE_TEMPLATE') ?>:
    </td>
    <td width="50%" class="adm-detail-content-cell-r">
        <textarea name="telegram_message_template" autocomplete="off" rows="5"
                  cols="42"><?= htmlspecialchars($settings['telegram']['messageTemplate'] ?? '') ?></textarea>
    </td>
</tr>
<tr>
    <td colspan="2">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_TELEGRAM_INSTRUCTION') ?>
    </td>
</tr>
