<?php

declare(strict_types=1);

use Bitrix\Main\Localization\Loc;
use Mediaca\Crossposting\Iblock\IblockGateway;
use Mediaca\Crossposting\Iblock\IblockTypeGateway;

$types = (new IblockTypeGateway())->fetchAll();
$iblocks = (new IblockGateway())->fetchAll();

$useIblocks = $config['main']['iblocks'] ?? [];
$notifyCreatedTasks = $config['main']['notifyCreatedTasks'] ?? false;
?>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_MAIN_USE_IBLOCKS') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="main[iblocks][]" size="6" multiple><?php
            foreach ($types as $type) {
                $filteredIblocks = array_filter($iblocks, static fn($iblock) => $iblock['typeId'] === $type['id']);
                ?>
            <optgroup label="— <?= htmlspecialchars($type['name']) ?>">
                <?php foreach ($filteredIblocks as $iblock) { ?>
                <option value="<?= $iblock['id'] ?>"<?= (in_array($iblock['id'], $useIblocks, true) ? 'selected' : '') ?>>
                    — — <?= htmlspecialchars($iblock['name']) ?></option><?php
                } ?>
                </optgroup><?php
            }
?></select>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('MEDIACA_CROSSPOSTING_MAIN_NOTIFY_CREATED_TASKS') ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="main[notify_created_tasks]"<?= ($notifyCreatedTasks ? ' checked' : '') ?>/>
    </td>
</tr>