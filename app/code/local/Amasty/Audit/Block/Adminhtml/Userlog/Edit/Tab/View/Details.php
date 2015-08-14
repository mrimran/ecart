<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */   
class Amasty_Audit_Block_Adminhtml_Userlog_Edit_Tab_View_Details extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        if(Mage::registry('current_log') && (Mage::registry('current_log')->getCategoryName() == "Cache" || Mage::registry('current_log')->getCategoryName() == "Index Management")) {
            $this->setTemplate('amaudit/tab/view/detailsCache.phtml');    
        }
        else {
            $this->setTemplate('amaudit/tab/view/details.phtml');    
        }

    }
    
    public function getLogRows() 
    {
         $collection = Mage::getModel('amaudit/log_details')->getCollection();
         if (!Mage::registry('current_log')) 
         {
            return array();
         }
         else
         {
            $collection->addFieldToFilter('log_id', array('in' => Mage::registry('current_log')->getId()));
            $collection->getSelect()->order('model');
             $date = new DateTime();
             $timeStamp = $date->getTimestamp();
             $fifeYears = 157680000;
             foreach ($collection as $el) {
                 if (strtotime($el->getOldValue()) && ($timeStamp - strtotime($el->getOldValue())) > $fifeYears) {
                     $collection->getItemById($el->getEntityId())->setOldValue(Mage::getModel('core/date')->date(null, $el->getOldValue()));
                 }
                 if (strtotime($el->getNewValue()) && ($timeStamp - strtotime($el->getNewValue())) > $fifeYears) {
                     $collection->getItemById($el->getEntityId())->setNewValue(Mage::getModel('core/date')->date(null, $el->getNewValue()));
                 }
             }
            return $collection;
         }
    }

}
