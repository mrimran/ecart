<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */

$this->startSetup();

$dataTable = $this->getTable('amaudit/data');
$fieldsSql = 'SHOW COLUMNS FROM ' . $dataTable;
$cols = $this->getConnection()->fetchCol($fieldsSql);

if (!in_array('location', $cols))
{
    $this->run("
        ALTER TABLE `{$dataTable}`  ADD `location` VARCHAR(255) NOT NULL, ADD `country_id` VARCHAR(3) NOT NULL
    ");
}

$this->endSetup();
