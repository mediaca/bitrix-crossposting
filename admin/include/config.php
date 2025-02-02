<?php

declare(strict_types=1);


use Mediaca\Crossposting\ChannelConfig\TelegramChannelConfig;
use Mediaca\Crossposting\ChannelConfig\VkChannelConfig;
use Mediaca\Crossposting\Task\Channel;
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
CJSCore::Init([$moduleId]);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

$APPLICATION->SetTitle(Loc::getMessage('MEDIACA_CROSSPOSTING_TITLE'));

$server = Context::getCurrent()->getServer();
$request = Context::getCurrent()->getRequest();

$domain = Option::get('main', 'server_name');
$domain = ($request->isHttps() ? 'https://' : 'http://') . ($domain ?: $server->getServerName());
$redirectUri = new Uri($domain . $request->getRequestUri());
$redirectUri->deleteParams(['lang', 'request-authorization-code', '_r']);

$config = Configuration::getValue($moduleId);
$vkConfig = new VkChannelConfig($config[Channel::VK->value] ?? []);

if ($request->isPost()) {
    $config['main']['iblocks'] = array_map('intval', $request->getPost('main')['iblocks'] ?? []);

    $vkConfig = VkChannelConfig::byFormData($request->getPost(Channel::VK->value));
    $config[Channel::VK->value] = $vkConfig->getValues();

    $telegramConfig = TelegramChannelConfig::byFormData($request->getPost(Channel::TELEGRAM->value));
    $config[Channel::TELEGRAM->value] = $telegramConfig->getValues();

    Configuration::setValue($moduleId, $config);
} else {
    $vkConfig = new VkChannelConfig($config[Channel::VK->value] ?? []);
    $telegramConfig = new TelegramChannelConfig($config[Channel::TELEGRAM->value] ?? []);
}

$vkIdClient = $vkConfig->clientId ? new VkIdClient(new HttpClient(), $vkConfig->clientId) : null;

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

    $vkConfig->accessToken = $response['access_token'];
    $vkConfig->refreshToken = $response['refresh_token'];
    $vkConfig->idToken = $response['id_token'];
    $vkConfig->deviceId = $request->get('device_id');

    $config[Channel::VK->value] = $vkConfig->getValues();

    Configuration::setValue($moduleId, $config);

    $successUri = new Uri($domain . $request->getRequestUri());
    $successUri->deleteParams(['code', 'device_id', 'expires_in', 'ext_id', 'state', 'type']);

    LocalRedirect($successUri->getUri(), true);
}

$tabs = [
    [
        'DIV' => 'mediaca_crossposting_main',
        'TAB' => Loc::getMessage('MEDIACA_CROSSPOSTING_MAIN_TITLE'),
        'TITLE' => Loc::getMessage('MEDIACA_CROSSPOSTING_MAIN_TITLE'),
        'file' => __DIR__ . '/part/config-main.php',
    ],
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
<?php

echo bitrix_sessid_post();

foreach ($tabs as $tab) {
    $tabControl->BeginNextTab();

    require $tab['file'];
}

$tabControl->buttons([]);

?></form><?php

$tabControl->end();

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
