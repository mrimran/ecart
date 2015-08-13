<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
class Amasty_Xnotif_Block_Adminhtml_Price_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('priceGrid');
        $this->setDefaultSort('cnt');
    }
    
    protected function _prepareCollection()
    {
        $stockAlertTable = Mage::getSingleton('core/resource')->getTableName('productalert/price');
        $collection = Mage::getModel('amxnotif/product')->getCollection();
        $collection->addAttributeToSelect('name'); 

        $select = $collection->getSelect();        
        $select->joinRight(array('s'=> $stockAlertTable), 's.product_id = e.entity_id', array('cnt' => 'count(s.product_id)', 'last_d'=>'MAX(add_date)', 'first_d'=>'MIN(add_date)', 'product_id'))
               ->where('status=0')
               ->group(array('s.product_id'));

        $select->columns(array('website_id' => new Zend_Db_Expr("SUBSTRING( GROUP_CONCAT( `s`.`website_id` ) , 1, 100 )")));
    
        $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
        if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
            $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
            $select->order($columnId . ' ' . $dir);
        }

        $collection->setIsCustomerMode(TRUE);
        $this->setCollection($collection);
        return parent::_prepareCollection(); 

    }

    protected function _prepareColumns()
    {
        $hlp =  Mage::helper('amxnotif'); 
    
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website',
                array(
                    'header'=> $hlp->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'website_id',
                    'renderer'  => 'Amasty_Xnotif_Block_Adminhtml_Stock_Renderer_Website',
                    'filter'  => false,
            ));
        } 
        
        $this->addColumn('name', array(
            'header'    => $hlp->__('Name'),
            'index'     => 'name',
        )); 
        
        $this->addColumn('sku', array(
            'header'    => $hlp->__('SKU'),
            'index'     => 'sku',
        ));
        
        $this->addColumn('cnt', array(
            'header'      => $hlp->__('Count'),
            'index'       => 'cnt',
            'filter'  => false,
        ));
        
        $this->addColumn('first_d', array(
            'header'    => $hlp->__('First Subscription'),
            'index'     => 'first_d',
            'type'      => 'datetime', 
            'width'     => '150px',
            'gmtoffset' => true,
            'default'	=> ' ---- ',
            'filter'  => false,
        ));
        $this->addColumn('last_d', array(
            'header'    => $hlp->__('Last Subscription'),
            'index'     => 'last_d',
            'type'      => 'datetime', 
            'width'     => '150px',
            'gmtoffset' => true,
            'default'	=> ' ---- ',
            'filter'  => false,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getProductId())); 
    }
}