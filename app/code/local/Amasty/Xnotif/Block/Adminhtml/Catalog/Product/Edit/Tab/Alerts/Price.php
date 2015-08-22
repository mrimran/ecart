<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */ 
class Amasty_Xnotif_Block_Adminhtml_Catalog_Product_Edit_Tab_Alerts_Price extends  Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts_Price
{
    protected function _prepareColumns()
    {    
        $this->addColumn('firstname', array(
            'header'    => Mage::helper('catalog')->__('First Name'),
            'index'     => 'firstname',
            'renderer'  => 'amxnotif/adminhtml_catalog_product_edit_tab_alerts_renderer_firstName',    
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('catalog')->__('Last Name'),
            'index'     => 'lastname',
            'renderer'  => 'amxnotif/adminhtml_catalog_product_edit_tab_alerts_renderer_lastName',
        ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('catalog')->__('Email'),
            'index'     => 'email',
            'renderer'  => 'amxnotif/adminhtml_catalog_product_edit_tab_alerts_renderer_email',
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'index'     => 'price',
            'type'      => 'currency',
            'currency_code'
            => Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE)
        ));

        $this->addColumn('add_date', array(
            'header'    => Mage::helper('catalog')->__('Date Subscribed'),
            'index'     => 'add_date',
            'type'      => 'date'
        ));

        $this->addColumn('last_send_date', array(
            'header'    => Mage::helper('catalog')->__('Last Notification'),
            'index'     => 'last_send_date',
            'type'      => 'date'
        ));

        $this->addColumn('send_count', array(
            'header'    => Mage::helper('catalog')->__('Send Count'),
            'index'     => 'send_count',
        ));
        
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}
  
