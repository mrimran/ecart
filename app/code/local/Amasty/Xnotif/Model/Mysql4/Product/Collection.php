<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */

if(class_exists("Mage_Catalog_Model_Resource_Product_Collection"))
{
	class Amasty_Xnotif_Model_Mysql4_Product_Collection_Pure extends Mage_Catalog_Model_Resource_Product_Collection{}
}
else 
{
	class Amasty_Xnotif_Model_Mysql4_Product_Collection_Pure extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection{}
}

class Amasty_Xnotif_Model_Mysql4_Product_Collection extends  Amasty_Xnotif_Model_Mysql4_Product_Collection_Pure
{
    /**
     * Customer mode flag
     *
     * @var bool
     */
    protected $_customerModeFlag = false;
    
    public function getSelectCountSql()
    {
        if ($this->getIsCustomerMode()) {
            $this->_renderFilters();

            $unionSelect = clone $this->getSelect();

            $unionSelect->reset(Zend_Db_Select::COLUMNS);
            $unionSelect->columns('e.entity_id');

            $unionSelect->reset(Zend_Db_Select::ORDER);
            $unionSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $unionSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

            $countSelect = clone $this->getSelect();
            $countSelect->reset();
            $countSelect->from(array('a' => $unionSelect), 'COUNT(*)');
        } else {
            $countSelect = parent::getSelectCountSql();
        }

        return $countSelect;
    }

    /**
     * Set customer mode flag value
     *
     * @param bool $value
     * @return Mage_Sales_Model_Resource_Order_Grid_Collection
     */
    public function setIsCustomerMode($value)
    {
        $this->_customerModeFlag = (bool)$value;
        return $this;
    }

    /**
     * Get customer mode flag value
     *
     * @return bool
     */
    public function getIsCustomerMode()
    {
        return $this->_customerModeFlag;
    }
}