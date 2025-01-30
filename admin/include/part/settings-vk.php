<?php

declare(strict_types=1);


use Mediaca\Crossposting\Vk\Id\VkIdClient;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global array $config
 * @global VkIdClient|null $vkIdClient
 * @global \Bitrix\Main\HttpRequest $request
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

$request = Context::getCurrent()->getRequest();
$server = Context::getCurrent()->getServer();

$domain = Option::get('main', 'server_name');
$domain = ($request->isHttps() ? 'https://' : 'http://') . ($domain ?: $server->getServerName());

?>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_VK_CLIENT_ID') ?>:
    </td>
    <td width="50%" class="adm-detail-content-cell-r">
        <input type="text" name="vk_client_id" size="10" autocomplete="off"
               value="<?= ($config['vk']['clientId'] ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_VK_OWNER_ID') ?>:
    </td>
    <td width="50%" class="adm-detail-content-cell-r">
        <input type="text" name="vk_owner_id" size="10" autocomplete="off"
               value="<?= ($config['vk']['ownerId'] ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_VK_FROM_GROUP') ?>:
    </td>
    <td width="50%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="vk_from_group"<?= (!empty($config['vk']['fromGroup']) ? ' checked' : '') ?>/>
    </td>
</tr>


<?php
if ($vkIdClient) {
    ?>
    <tr>
    <td width="50%" class="adm-detail-content-cell-l"
    </td>
    <td width="50%" class="adm-detail-content-cell-r"><?php
        $tokensUrl = new Uri($request->getRequestUri());
    $tokensUrl->addParams(['request-authorization-code' => true]);
    ?>
        <a href="<?= $tokensUrl->getUri() ?>"><?= !empty($config['vk']['accessToken']) ?
            Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_VK_UPDATE_TOKENS') :
            Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_VK_GET_TOKENS') ?></a>
    </td>
    </tr><?php
}
?>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_VK_ACCESS_TOKEN') ?>:
    </td>
    <td width="50%" class="adm-detail-content-cell-r">
        <input type="text" size="40" disabled
               value="<?= htmlspecialchars($config['vk']['accessToken'] ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_VK_REFRESH_TOKEN') ?>:
    </td>
    <td width="50%" class="adm-detail-content-cell-r">
        <input type="text" size="40" disabled
               value="<?= htmlspecialchars($config['vk']['refreshToken'] ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_VK_ID_TOKEN') ?>:
    </td>
    <td width="50%" class="adm-detail-content-cell-r">
        <input type="text" size="40" disabled
               value="<?= htmlspecialchars($config['vk']['idToken'] ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_VK_DEVICE_ID') ?>:
    </td>
    <td width="50%" class="adm-detail-content-cell-r">
        <input type="text" size="40" disabled
               value="<?= htmlspecialchars($config['vk']['deviceId'] ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="50%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_VK_MESSAGE_TEMPLATE') ?>:
    </td>
    <td width="50%" class="adm-detail-content-cell-r">
        <textarea name="vk_message_template" autocomplete="off" rows="5"
                  cols="42"><?= htmlspecialchars($config['vk']['messageTemplate'] ?? '') ?></textarea>
    </td>
</tr>
<tr>
    <td colspan="2">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_SETTINGS_VK_INSTRUCTION', ['#DOMAIN#' => $domain]) ?>
    </td>
</tr>
