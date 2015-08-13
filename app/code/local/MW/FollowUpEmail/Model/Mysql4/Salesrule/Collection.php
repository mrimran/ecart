<?php
class MW_FollowUpEmail_Model_Mysql4_Salesrule_Collection extends Mage_SalesRule_Model_Mysql4_Rule_Collection
{
    public function setValidationFilter($websiteId, $customerGroupId, $couponCode = '', $now = null)
    {		
        if (!$this->getFlag('validation_filter')) {

            /* We need to overwrite joinLeft if coupon is applied */
            $this->getSelect()->reset();
            parent::_initSelect();
			$now = Mage::getModel('core/date')->date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT);
            $this->addFieldToFilter('website_ids', array('finset' => (int)$websiteId))
            /*->addFieldToFilter('customer_group_ids', array('finset' => (int)$customerGroupId))*/
            ->addFieldToFilter('is_active', 1);
            $select = $this->getSelect();

            if (strlen($couponCode)) {
                $select->joinLeft(
                    array('fue_coupons' => $this->getTable('followupemail/coupons')),
                    'main_table.rule_id = fue_coupons.sale_rule_id ',
                    array('code')
                );
            	$select->where('(main_table.coupon_type != ? ', Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON)                
                ->where('fue_coupons.code = ?)', $couponCode)
                ->where('fue_coupons.coupon_status	 = ?',MW_FollowUpEmail_Model_System_Config_Statuscoupon::COUPON_STATUS_SENT)
				->where('fue_coupons.expiration_date is null or fue_coupons.expiration_date >= ?',  $now);				
            } else {
                $this->addFieldToFilter('main_table.coupon_type', Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON);
            }
            $this->setOrder('sort_order', self::SORT_ORDER_ASC);
            $this->setFlag('validation_filter', true);
        }

        return $this;
    }
}
