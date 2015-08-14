<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */       
class Amasty_Xnotif_Block_Abstract extends Mage_Core_Block_Template
{
    protected $_title;
    protected $_type;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amasty/amxnotif/subscr.phtml');
     
        $this->_loadCollection();
    }
    
    private function _loadCollection(){
        $tableName =  'productalert/' . $this->_type;
        $alertTable = Mage::getSingleton('core/resource')->getTableName($tableName);
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('name');             
                    
        $select = $collection->getSelect();
        $entityIdName = 'alert_' . $this->_type . '_id';
        $select->joinInner(array('s'=> $alertTable), 's.product_id = e.entity_id', array( 'add_date', $entityIdName,'parent_id'))
               ->where('s.status=0')
               ->where('customer_id=? OR email=?', Mage::getSingleton('customer/session')->getCustomer()->getId(), Mage::getSingleton('customer/session')->getCustomer()->getEmail())
               ->group(array('s.product_id'));
          
        $this->setSubscriptions($collection);    
    }

    public function getProduct($id){
        return Mage::getModel('catalog/product')->load($id);
    }
    
    public function getRemoveUrl($order){
        $entityIdName = 'alert_' . $this->_type . '_id';
        
        $id =  $order->getData($entityIdName);
        return Mage::getUrl('amxnotif/' . $this->_type . '/remove',
            array('item' => $id)
        );
    }
    
    public function getProductUrl($_order){
        $url = $_order->getParentId()? $this->getProduct($_order->getParentId())->getProductUrl(): $this->getProduct($_order->getEntityId())->getProductUrl();
        
        if (isset($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "")
        {
            $url = str_replace('http:', 'https:', $url);
        }
        return $url;
    }
}
 