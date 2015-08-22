<?php

class MW_FollowUpEmail_Model_Mysql4_Abandoncartforguest_Collection extends Mage_Reports_Model_Resource_Quote_Collection

{
	public function prepareForAbandonedReport($storeIds, $filter = null)
    {
		$customerEmails = Mage::getModel('customer/customer')->getCollection()->getColumnValues("email");
        $this->addFieldToFilter('items_count', array('neq' => '0'))
            ->addFieldToFilter('main_table.is_active', '1')            
            ->addFieldToFilter('main_table.customer_email', array('nin' => $customerEmails))            
           	->addSqlCondition()
            ->setOrder('updated_at');
        if (is_array($storeIds) && !empty($storeIds)) {
            $this->addFieldToFilter('store_id', array('in' => $storeIds));
        }


        return $this;
    }
	
	public function addSqlCondition(){
		
		$resource = Mage::getSingleton('core/resource');
		
		$this->getSelect()

            ->joinLeft(array('a' => $resource->getTableName('sales/quote_address')),

            'main_table.entity_id=a.quote_id AND a.address_type="billing"',

            array(

                'customer_email' => new Zend_Db_Expr('IFNULL(main_table.customer_email, a.email)'),

                'customer_firstname' => new Zend_Db_Expr('IFNULL(main_table.customer_firstname, a.firstname)'),

                'customer_middlename' => new Zend_Db_Expr('IFNULL(main_table.customer_middlename, a.middlename)'),

                'customer_lastname' => new Zend_Db_Expr('IFNULL(main_table.customer_lastname, a.lastname)'),

				'city' => 'a.city',

                'state' => 'a.region',

                'zipcode' => 'a.postcode',

                'country_id' => 'a.country_id',			
				'username' => new Zend_Db_Expr('GROUP_CONCAT(customer_firstname,customer_lastname)'),

            ))

            ->joinInner(array('i' => $resource->getTableName('sales/quote_item')), 'main_table.entity_id=i.quote_id', array(

            'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.product_id)'),

            'item_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.item_id)'),

			'sku' => new Zend_Db_Expr('GROUP_CONCAT(i.sku)'),

            'product_type' => new Zend_Db_Expr('GROUP_CONCAT(i.product_type)')

        	))

            ->where('main_table.is_active=1') 			

            ->where('main_table.items_count>0')

            ->where('main_table.customer_email IS NOT NULL OR a.email IS NOT NULL')

            ->where('i.parent_item_id IS NULL')

            ->group('main_table.entity_id')

            ->order('updated_at');			
		
		return $this;
	}
	
	
    public function addStoreFilter($storeIds)
    {
        $this->addFieldToFilter('store_id', array('in' => $storeIds));
        return $this;
    }

}