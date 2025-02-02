<?php


use Bitrix\Main\Application;
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Mediaca\Crossposting\CrosspostingTab;
use Mediaca\Crossposting\SenderAgent;
use Mediaca\Crossposting\TaskAdder;

Loc::loadMessages(__FILE__);


class mediaca_crossposting extends CModule
{
    public $MODULE_ID = 'mediaca.crossposting';
    public $MODULE_GROUP_RIGHTS = 'N';
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    private readonly string $moduleDir;
    private readonly string $adminIncludeDir;
    private readonly string $bitrixDir;

    public function __construct()
    {
        $this->MODULE_NAME = Loc::getMessage('MEDIACA_CROSSPOSTING_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MEDIACA_CROSSPOSTING_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('MEDIACA_CROSSPOSTING_PARTNER_NAME');
        $this->PARTNER_URI = 'https://www.artlebedev.ru/';

        require(__DIR__ . '/version.php');
        if (!empty($arModuleVersion['VERSION']) && !empty($arModuleVersion['VERSION_DATE'])) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->moduleDir = dirname(__DIR__) . '/';
        $this->adminIncludeDir = $this->moduleDir . 'admin/include/';
        $this->bitrixDir = Application::getDocumentRoot() . '/bitrix/';
    }


    public function DoInstall(): void
    {
        $this->InstallDB();
        $this->installFiles();
        $this->registerEventsHandlers();

        CAgent::AddAgent('\\' . SenderAgent::class . '::run();', $this->MODULE_ID, 'N', 120);

        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function InstallDB(): void
    {
        $file = $this->moduleDir . '/install/db/install.sql';
        if (!is_readable($file)) {
            throw new \RangeException("File $file is not available for reading");
        }

        $errors = Application::getConnection()->executeSqlBatch(file_get_contents($file));
        if ($errors) {
            throw new \DomainException("SQL queries failed with errors: " . implode(', ', $errors));
        }
    }

    public function installFiles(): bool
    {
        $relativeDirFrom = str_replace(Application::getDocumentRoot(), '', $this->adminIncludeDir);

        foreach ($this->getFileDataList() as $fileData) {
            $code = "<?php\n" . 'require_once($_SERVER[\'DOCUMENT_ROOT\']' . " . '$relativeDirFrom{$fileData['originalName']}');";

            file_put_contents("{$this->bitrixDir}admin/{$fileData['saveName']}", $code);
        }

        CopyDirFiles("{$this->moduleDir}install/css", "{$this->bitrixDir}css/$this->MODULE_ID", true, true);
        CopyDirFiles("{$this->moduleDir}install/images", "{$this->bitrixDir}images/$this->MODULE_ID", true, true);

        return true;
    }

    private function getFileDataList(): array
    {
        $result = [];
        foreach (scandir($this->adminIncludeDir) as $name) {
            if ($name === '.' || $name === '..' || !is_file($this->adminIncludeDir . $name)) {
                continue;
            }

            $result[] = [
                'originalName' => $name,
                'saveName' => $this->getAdminFileName($name),
            ];
        }

        return $result;
    }

    private function getAdminFileName(string $fileName): string
    {
        return str_replace('.', '-', $this->MODULE_ID) . '-' . $fileName;
    }

    private function registerEventsHandlers(): void
    {
        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnAdminIBlockElementEdit',
            $this->MODULE_ID,
            '\\' . CrosspostingTab::class,
            'getDescription',
            9999,
        );

        EventManager::getInstance()->registerEventHandler(
            'iblock',
            'OnAfterIBlockElementAdd',
            $this->MODULE_ID,
            '\\' . TaskAdder::class,
            'addByEvent',
            9999,
        );

        EventManager::getInstance()->registerEventHandler(
            'iblock',
            'OnAfterIBlockElementUpdate',
            $this->MODULE_ID,
            '\\' . TaskAdder::class,
            'addByEvent',
            9999,
        );
    }

    public function DoUninstall(): void
    {
        $config = Configuration::getInstance();
        $config->delete($this->MODULE_ID);
        $config->saveConfiguration();

        $this->DoUninstallDB();
        $this->doUninstallFiles();
        $this->unRegisterEventsHandlers();
        CAgent::RemoveAgent('\\' . SenderAgent::class . '::run();', $this->MODULE_ID);

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function doUninstallFiles(): bool
    {
        foreach ($this->getFileDataList() as $fileData) {
            unlink("{$this->bitrixDir}admin/{$fileData['saveName']}");
        }

        DeleteDirFilesEx("bitrix/css/$this->MODULE_ID");
        DeleteDirFilesEx("bitrix/images/$this->MODULE_ID");

        return true;
    }

    public function DoUninstallDB(): void
    {
        $file = $this->moduleDir . '/install/db/uninstall.sql';
        if (!is_readable($file)) {
            throw new \RangeException("File $file is not available for reading");
        }

        $errors = Application::getConnection()->executeSqlBatch(file_get_contents($file));
        if ($errors) {
            throw new \DomainException("SQL queries failed with errors: " . implode(', ', $errors));
        }
    }

    private function unRegisterEventsHandlers(): void
    {
        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnAdminIBlockElementEdit',
            $this->MODULE_ID,
            '\\' . CrosspostingTab::class,
            'getDescription',
            '',
            '',
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'iblock',
            'OnAfterIBlockElementUpdate',
            $this->MODULE_ID,
            '\\' . TaskAdder::class,
            'addByEvent',
            '',
            '',
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'iblock',
            'OnAfterIBlockElementAdd',
            $this->MODULE_ID,
            '\\' . TaskAdder::class,
            'addByEvent',
            '',
            '',
        );
    }
}
