<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
class Amasty_Xnotif_Block_Adminhtml_Price extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_price';
        $this->_blockGroup = 'amxnotif';
        
        $scheduleCollection = Mage::getModel("cron/schedule")->getCollection()
                ->addFieldToFilter('job_code', array('eq' => 'catalog_product_alert'));
        
        $scheduleCollection->getSelect()->order("schedule_id desc");
        $scheduleCollection->getSelect()->limit(1);
        if ($scheduleCollection->getSize() > 0){
            $this->_headerText = Mage::helper('amxnotif')->__('Price Alerts');    
        } 
        else {
            $this->_headerText = Mage::helper('amxnotif')->__('Price Alerts ') 
                                                         . '<div style="font-size: 13px;">'
                                                         . Mage::helper('amxnotif')->__('No cron job "catalog_product_alert" found. Please check your cron configuration: <a href="https://support.amasty.com/index.php?/Knowledgebase/Article/View/79/25/i-cant-send-notifications">Read more</a>')
                                                         . '</div>';  
        }
        
        $this->_removeButton('add'); 
    }
}