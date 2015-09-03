<?php
/**
 *
 * @category Magetools
 * @package Magetools_OptionFilter
 * @copyright Copyright (c) 2014 Magetools Magetools.net
 * @author Magetools
 *
 */
try{
  $installer = $this;
  $installer->startSetup();
  $installer->getConnection()->addColumn($installer->getTable('catalog/product_option'), 'option_code', 'VARCHAR(64) NULL');
  $installer->endSetup();
}catch (Exception $e){
  Mage::log($e->getMessage());
  Mage::throwException('Magetools_OptionFilter installer exception: "'.$e->getMessage());
}