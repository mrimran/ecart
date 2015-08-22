<?php
    class MW_FollowUpEmail_Model_System_Config_Sender extends Mage_Core_Model_Abstract
    {
        public function toOptionArray()
        {
			/*//Gerneral contact
			Mage::getStoreConfig('trans_email/ident_gerneral/name');
			Mage::getStoreConfig('trans_email/ident_gerneral/email');

			//Sales Representative
			Mage::getStoreConfig('trans_email/ident_sales/name');
			Mage::getStoreConfig('trans_email/ident_sales/email');

			//Customer Support
			Mage::getStoreConfig('trans_email/ident_support/name');
			Mage::getStoreConfig('trans_email/ident_support/email');

			//Custom email1
			Mage::getStoreConfig('trans_email/ident_custom1/name');
			Mage::getStoreConfig('trans_email/ident_custom1/email');

			//Custom email2
			Mage::getStoreConfig('trans_email/ident_custom2/name');
			Mage::getStoreConfig('trans_email/ident_custom2/email');*/ 
            return array(
                array('value' => 'general', 'label'=>Mage::helper('followupemail')->__('General Contact')),
                array('value' => 'sales', 'label'=>Mage::helper('followupemail')->__('Sales Representative')),
                array('value' => 'support', 'label'=>Mage::helper('followupemail')->__('Customer Support')),            
                array('value' => 'custom1', 'label'=>Mage::helper('followupemail')->__('Custom Email 1')),            
                array('value' => 'custom2', 'label'=>Mage::helper('followupemail')->__('Custom Email 2')),            
            );        
        }
    }
?>