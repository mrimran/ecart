<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */  

if(class_exists("Mage_ProductAlert_Model_Resource_Price_Customer_Collection"))
{
	class Amasty_Xnotif_Model_Resource_Price_Customer_Collection_Pure extends Mage_ProductAlert_Model_Resource_Price_Customer_Collection{}
}
else 
{
	class Amasty_Xnotif_Model_Resource_Price_Customer_Collection_Pure extends Mage_ProductAlert_Model_Mysql4_Price_Customer_Collection{}
}

class Amasty_Xnotif_Model_Resource_Price_Customer_Collection extends Amasty_Xnotif_Model_Resource_Price_Customer_Collection_Pure  
{ 
    public function join($productId, $websiteId)
    {
        $this->getSelect()->joinRight(
                                array('alert' => $this->getTable('productalert/price')),
                                'alert.customer_id=e.entity_id',
                                array( 'add_date', 'last_send_date', 'send_count', 'status','guest_email' => 'email')
                            )
                         ->reset( Zend_Db_Select::WHERE )
                         ->where('alert.product_id=?', $productId)
                         ->group('alert.email')->group('e.email');
        if ($websiteId) {
            $this->getSelect()->where('alert.website_id=?', $websiteId);
        }
        $this->_setIdFieldName('alert_price_id');
        $this->addAttributeToSelect('*');
        $this->setIsCustomerMode(TRUE);
        
        return $this;
    }
    
     protected $_customerModeFlag = false;

    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->_orders[$field] = $direction;
        
        if($field == "email") $field = "guest_email";
        $this->getSelect()->order($field . ' ' . $direction);
        
        return $this;
    }
    
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
