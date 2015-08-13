<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */  
$this->startSetup();
try{
    $tableName = Mage::getSingleton('core/resource')->getTableName('core/config_data');
    $fieldsSql = 'SELECT * FROM ' . $tableName . " WHERE `path` like 'catalog/productalert/allow_stock'";
    $cols = $this->getConnection()->fetchCol($fieldsSql);
    if ($cols)
    {
        $this->run("
            UPDATE `{$tableName}` SET `value` = '1' WHERE `path` = 'catalog/productalert/allow_stock';
        ");
    }
    else{
        $this->run("
            INSERT into `{$tableName}`(`scope`, `scope_id`, `path`, `value`) VALUES ('default', 0, 'catalog/productalert/allow_stock', '1');
        ");
    } 
}
catch(Exception $exc){
    Mage::log($exc->getMessage());
}
$this->endSetup();        