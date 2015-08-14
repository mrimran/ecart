<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
class Amasty_Xnotif_Block_Adminhtml_Stock_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('stockGrid');
        $this->setDefaultSort('cnt');
    }
    
    protected function _prepareCollection()
    {
        $stockAlertTable = Mage::getSingleton('core/resource')->getTableName('productalert/stock');
        $collection = Mage::getModel('amxnotif/product')->getCollection();
        $collection->addAttributeToSelect('name')
                    ->addAttributeToFilter(
                        'status',
                        array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                    );

        $select = $collection->getSelect();

        $select->joinRight(array('s'=> $stockAlertTable), 's.product_id = e.entity_id', array('total_cnt' => 'count(s.product_id)', 'cnt' => 'COUNT( NULLIF(`s`.`status`, 1) )', 'last_d'=>'MAX(add_date)', 'first_d'=>'MIN(add_date)', 'product_id'))
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

        $this->addColumn('total_cnt', array(
            'header'      => $hlp->__('Total Number of Subscriptions'),
            'index'       => 'total_cnt',
            'filter'  => false,
            'align' => 'center',
            'width'     => '150px'
        ));

        $this->addColumn('cnt', array(
            'header'      => $hlp->__('Customers Awaiting Notification'),
            'index'       => 'cnt',
            'filter'  => false,
            'frame_callback' => array($this, 'addColors'),
            'width'     => '150px'
        ));

        return parent::_prepareColumns();
    }

    public function addColors($value, $row, $column)
    {
        switch($value){
            case 0: $color = "green";
                break;
            case 1: $color = "lightcoral";
                break;
            case 2: $color = "indianred";
                break;
            case 3: $color = "brown";
                break;
            case 4: $color = "firebrick";
                break;
            case 4: $color = "darkred";
                break;
            default: $color = "red";
        }

        return '<div style="width: 50px; margin: 0 auto; border-radius: 3px;text-align: center; background-color: ' . $color .'">' .
                    $value .
                '</div>';
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getProductId())); 
    }
}