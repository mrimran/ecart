<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */

$this->startSetup();

$dataTable = $this->getTable('amaudit/active');


$this->run("
    ALTER TABLE `{$dataTable}`  ADD `username` VARCHAR(80) NOT NULL ,  ADD `date_time` DATETIME NOT NULL ,  ADD `name` VARCHAR(80) NOT NULL ,  ADD `ip` VARCHAR(20) NOT NULL ,  ADD `location` VARCHAR(100) NOT NULL ,  ADD `county_id` VARCHAR(3) NOT NULL;
    ALTER TABLE `{$dataTable}` DROP `data_id`
");


$this->endSetup();
