<?php


use Bitrix\Main\Application;
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

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
    private readonly string $dirAdminFrom;
    private readonly string $dirAdminTo;

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
        $this->dirAdminFrom = dirname(__DIR__) . '/admin/include/';
        $this->dirAdminTo = Application::getDocumentRoot() . '/bitrix/admin/';
    }


    public function DoInstall(): void
    {
        $this->InstallDB();
        $this->installFiles();

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
        $relativeDirFrom = str_replace(Application::getDocumentRoot(), '', $this->dirAdminFrom);

        foreach ($this->getFileDataList() as $fileData) {
            $code = "<?php\n"
                . 'require_once($_SERVER[\'DOCUMENT_ROOT\'] . \'' . $relativeDirFrom . $fileData['originalName'] . '\');';

            file_put_contents($this->dirAdminTo . $fileData['saveName'], $code);
        }

        return true;
    }

    private function getFileDataList(): array
    {
        if (!is_dir($this->dirAdminFrom)) {
            return [];
        }

        $result = [];
        foreach (scandir($this->dirAdminFrom) as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }

            if (!is_file($this->dirAdminFrom . $name)) {
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


    public function DoUninstall(): void
    {
        $config = Configuration::getInstance();
        $config->delete($this->MODULE_ID);
        $config->saveConfiguration();

        $this->DoUninstallDB();
        $this->doUninstallFiles();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function doUninstallFiles(): bool
    {
        foreach ($this->getFileDataList() as $fileData) {
            unlink($this->dirAdminTo . $fileData['saveName']);
        }

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
}
