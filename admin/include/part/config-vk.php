<?php

declare(strict_types=1);

use Mediaca\Crossposting\Config\VkChannelConfig;
use Mediaca\Crossposting\Module;
use Mediaca\Crossposting\Task\Channel;
use Mediaca\Crossposting\Vk\Id\VkIdClient;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global VkChannelConfig $vkConfig
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
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_VK_CLIENT_ID') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="<?= Channel::VK->value?>[client_id]" size="10" autocomplete="off"
               value="<?= ($vkConfig->clientId ?? '') ?>"/>
    </td>
</tr>
<?php if ($vkIdClient) { ?>
    <tr>
    <td width="40%" class="adm-detail-content-cell-l"></td>
    <td width="60%" class="adm-detail-content-cell-r"><?php
        $tokensUrl = new Uri($request->getRequestUri());
    $tokensUrl->addParams(['request-authorization-code' => true]);
    ?>
        <a href="<?= $tokensUrl->getUri() ?>"
           class="mediaca-crossposting-vk-button">
            <img src="/bitrix/images/<?= Module::ID ?>/vk-logo.svg" class="mediaca-crossposting-vk-button__icon">
            <span class="mediaca-crossposting-vk-button_text"><?= $vkConfig->accessToken ? Loc::getMessage('MEDIACA_CROSSPOSTING_VK_UPDATE_TOKENS')
                : Loc::getMessage('MEDIACA_CROSSPOSTING_VK_GET_TOKENS') ?></span>
        </a>
    </td>
    </tr><?php
} ?>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_VK_ACCESS_TOKEN') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="<?= Channel::VK->value?>[access_token]" size="40" disabled
               value="<?= htmlspecialchars($vkConfig->accessToken ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_VK_REFRESH_TOKEN') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="<?= Channel::VK->value?>[refresh_token]" size="40" disabled
               value="<?= htmlspecialchars($vkConfig->refreshToken ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_VK_ID_TOKEN') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="<?= Channel::VK->value?>[id_token]" size="40" disabled
               value="<?= htmlspecialchars($vkConfig->idToken ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_VK_DEVICE_ID') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="<?= Channel::VK->value?>[device_id]" size="40" disabled
               value="<?= htmlspecialchars($vkConfig->deviceId ?? '') ?>"/>
    </td>
</tr>

<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_VK_OWNER_ID') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="<?= Channel::VK->value?>[owner_id]" size="10" autocomplete="off"
               value="<?= ($vkConfig->ownerId ?? '') ?>"/>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_VK_FROM_GROUP') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="<?= Channel::VK->value?>[from_group]"<?= ($vkConfig->fromGroup ? ' checked' : '') ?>/>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_VK_MESSAGE_TEMPLATE') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <textarea name="<?= Channel::VK->value?>[message_template]" autocomplete="off" rows="5"
                  cols="42"><?= htmlspecialchars($vkConfig->messageTemplate ?? Loc::getMessage('MEDIACA_CROSSPOSTING_VK_MESSAGE_TEMPLATE_DEFAULT_VALUE')) ?></textarea>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_VK_DATA_PHOTOS') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="<?= Channel::VK->value?>[data_photos]" size="40" autocomplete="off"
               value="<?= htmlspecialchars($vkConfig->dataPhotos !== null
                   ? implode(',', $vkConfig->dataPhotos) : Loc::getMessage('MEDIACA_CROSSPOSTING_VK_DATA_PHOTOS_DEFAULT_VALUE')) ?>">
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_VK_USE_ALL_PHOTOS') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="<?= Channel::VK->value?>[use_all_photos]"
            <?= (!empty($config['vk']['useAllPhotos']) ? ' checked' : '') ?>/>
    </td>
</tr>
<tr>
    <td colspan="2">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_VK_INSTRUCTION', ['#DOMAIN#' => $domain]) ?>
    </td>
</tr>
