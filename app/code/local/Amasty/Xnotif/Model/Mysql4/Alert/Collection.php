<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
class Amasty_Xnotif_Model_Mysql4_Alert_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
       protected function _initSelect()
    {
     //   parent::_initSelect();
       
        $this->addAttributeToSelect('name');  
        $select = $this->getSelect();
        $productTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_entity');
        $select->joinInner(array('s'=> $productTable), 'e.product_id = s.entity_id', array('cnt' => 'count(e.product_id)', 'last_d'=>'MAX(add_date)', 'first_d'=>'MIN(add_date)', 'product_id', 'website_id'))
               ->where('send_count=0')
               ->group(array('e.website_id', 'e.product_id'));
       return $this;
    }
    
     public function getSelectCountSql()
     {
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::ORDER);
        return $this->getConnection()->select()->from($select, 'COUNT(*)');

     }
}
  