<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Block_Adminhtml_Userlog_Grid_Export extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('date_time');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amaudit/log')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection(); 

    }

    protected function _prepareColumns()
    {
        
        $hlp = Mage::helper('amaudit');

        $this->addColumn('date_time', array(
            'header'    => $hlp->__('Date'),
            'index'     => 'date_time',
            'type'      => 'datetime',
            'width'     => '170px',
        ))
        ;
       
        $this->addColumn('username', array(
            'header'    => $hlp->__('Username'),
            'index'     => 'username',
            'align'     => 'center',
        ));

        $this->addColumn('fullname', array(
            'header'    => $hlp->__('Full name'),
            'index'     => 'username',
            'align'     => 'center',
            'frame_callback' => array($this, 'showFullName'),
        ));
        
        $this->addColumn('type', array(
            'header'      => $hlp->__('Action Type'),
            'index'       => 'type',
            'align'     => 'center',
        ));
        
        $this->addColumn('category_name', array(
            'header'      => $hlp->__('Object'),
            'index'       => 'category_name',
        ));
        
        
        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'skipEmptyStoresLabel' => 1,
                'sortable'      => true,
            ));
        }

        $this->addColumn('info', array(
            'header'      => $hlp->__('Item'),
            'index'       => 'info',
            'align'     => 'center',
            //'renderer'  => 'amaudit/adminhtml_auditlog_renderer_name',
        )); 
        
        
        
        return parent::_prepareColumns();
    }
    
    private function getStoreOptions(){
        $array = Mage::app()->getStores(true);
        $options = array();
        foreach($array as $key => $value){
              $options[$key] = $value->getName();  
        }
        return $options;
    }

    public function showFullName($value, $row, $column, $isExport)
    {
        $username = $row->getUsername();
        if($username) {
            $user = Mage::getModel('admin/user')->loadByUsername($username);
            return $user->getName();
        }
        return '';
    }

    public function getRowUrl($row)
    {
          return $this->getUrl('*/*/edit', array('id' => $row->getId()));  
    }
    
}