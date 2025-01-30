<?php

declare(strict_types=1);

namespace Mediaca\Crossposting;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;

class Server
{
    public function getDomain(): string
    {
        $request = Context::getCurrent()->getRequest();
        $server = Context::getCurrent()->getServer();

        $domain = Option::get('main', 'server_name');

        return ($request->isHttps() ? 'https://' : 'http://') . ($domain ?: $server->getServerName());
    }

    public function getDocumentRoot(): string
    {
        $result = Application::getDocumentRoot();

        return !str_ends_with($result, '/') ? $result . '/' : $result;
    }
}
