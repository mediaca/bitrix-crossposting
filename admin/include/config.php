<?php

declare(strict_types=1);


use Mediaca\Crossposting\Vk\Id\AccessTokens;
use Mediaca\Crossposting\Vk\Id\CodeVerifier;
use Mediaca\Crossposting\Vk\Id\Scope;
use Mediaca\Crossposting\Vk\Id\VkIdClient;
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Uri;

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

global $USER, $APPLICATION;

if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

$moduleId = 'mediaca.crossposting';

Loc::loadMessages(__FILE__);
Loader::requireModule($moduleId);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

$APPLICATION->SetTitle(Loc::getMessage('MEDIACA_CROSSPOSTING_TITLE'));

$server = Context::getCurrent()->getServer();
$request = Context::getCurrent()->getRequest();

$domain = Option::get('main', 'server_name');
$domain = ($request->isHttps() ? 'https://' : 'http://') . ($domain ?: $server->getServerName());
$redirectUri = new Uri($domain . $request->getRequestUri());
$redirectUri->deleteParams(['lang', 'request-authorization-code']);

$config = Configuration::getValue($moduleId);

if ($request->isPost()) {
    $config['vk']['clientId'] = $request->getPost('vk_client_id') ?
        (int) $request->getPost('vk_client_id') : null;
    $config['vk']['ownerId'] = $request->getPost('vk_owner_id') ?
        (int) $request->getPost('vk_owner_id') : null;
    $config['vk']['fromGroup'] = (bool) $request->getPost('vk_from_group');
    $config['vk']['dataPhotos'] = array_map('trim', explode(',', $request->getPost('vk_data_photos')));
    $config['vk']['useAllPhotos'] = (bool) $request->getPost('vk_use_all_photos');

    $config['telegram']['accessToken'] = $request->getPost('telegram_access_token');
    $config['telegram']['chatUserName'] = $request->getPost('telegram_chat_user_name');
    $config['telegram']['messageTemplate'] = $request->getPost('telegram_message_template');
    $config['telegram']['dataPhotos'] = array_map('trim', explode(',', $request->getPost('telegram_data_photos')));

    Configuration::setValue($moduleId, $config);
}

$vkIdClient = !empty($config['vk']['clientId']) ?
    new VkIdClient(new HttpClient(), $config['vk']['clientId']) : null;

if (!empty($_GET['request-authorization-code']) && $vkIdClient) {
    $scopes = [Scope::WALL, Scope::PHOTOS];
    $codeVerifier = new CodeVerifier();
    $vkAuthorizeUrl = $vkIdClient->getAuthorizeUrl(
        $scopes,
        $redirectUri->getUri(),
        $codeVerifier,
        null,
    );

    $_SESSION['MEDIACA_CROSSPOSTING_VK_CODE_VERIFIER'] = $codeVerifier->value;

    LocalRedirect($vkAuthorizeUrl, true);
} elseif (
    $vkIdClient && $request->get('code') && $request->get('device_id')
    && $_SESSION['MEDIACA_CROSSPOSTING_VK_CODE_VERIFIER']) {
    $response = $vkIdClient->getAccessToken(
        $_SESSION['MEDIACA_CROSSPOSTING_VK_CODE_VERIFIER'],
        $request->get('code'),
        $request->get('device_id'),
        $redirectUri->getUri(),
        null,
    );

    $tokens = new AccessTokens(
        $response['access_token'],
        $response['refresh_token'],
        $response['id_token'],
        $request->get('device_id'),
    );

    $config['vk']['accessToken'] = $tokens->accessToken;
    $config['vk']['refreshToken'] = $tokens->refreshToken;
    $config['vk']['idToken'] = $tokens->idToken;
    $config['vk']['deviceId'] = $tokens->deviceId;

    Configuration::setValue($moduleId, $config);

    $successUri = new Uri($domain . $request->getRequestUri());
    $successUri->deleteParams(['code', 'device_id', 'expires_in', 'ext_id', 'state', 'type']);

    LocalRedirect($successUri->getUri(), true);
}

$tabs = [
    [
        'DIV' => 'mediaca_crossposting_vk',
        'TAB' => Loc::getMessage('MEDIACA_CROSSPOSTING_VK_TITLE'),
        'TITLE' => Loc::getMessage('MEDIACA_CROSSPOSTING_VK_TITLE'),
        'file' => __DIR__ . '/part/config-vk.php',
    ],
    [
        'DIV' => 'mediaca_crossposting_telegram',
        'TAB' => Loc::getMessage('MEDIACA_CROSSPOSTING_TELEGRAM_TITLE'),
        'TITLE' => Loc::getMessage('MEDIACA_CROSSPOSTING_TELEGRAM_TITLE'),
        'file' => __DIR__ . '/part/config-telegram.php',
    ],
];

$tabControl = new CAdminTabControl(
    'tabControl',
    $tabs,
);

$tabControl->begin();
?>
    <form action="<?= $request->getRequestUri() ?>" method="post">
        <?= bitrix_sessid_post() ?>
        <?php

        foreach ($tabs as $tab) {
            $tabControl->BeginNextTab();

            require $tab['file'];
        }

$tabControl->buttons([]);
?>
    </form>
<?php
$tabControl->end();

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
