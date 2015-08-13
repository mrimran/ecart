<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Block_Adminhtml_Auditlog_Grid_Export extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('date_time');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amaudit/data')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
        $hlp = Mage::helper('amaudit');

        $this->addColumn('date_time', array(
            'header' => $hlp->__('Date'),
            'index' => 'date_time',
            'type' => 'datetime',
            'width' => '170px',
        ));

        $this->addColumn('username', array(
            'header' => $hlp->__('Username'),
            'index' => 'username',
        ));

        $this->addColumn('name', array(
            'header' => $hlp->__('Full Name'),
            'index' => 'name',
        ));

        $this->addColumn('ip', array(
            'header' => $hlp->__('IP Address'),
            'index' => 'ip',
        ));

        $this->addColumn('location', array(
            'header' => $hlp->__('Location'),
            'index' => 'location',
        ));

        $this->addColumn('status', array(
            'header' => $hlp->__('Status'),
            'index' => 'status',
            'width' => '120',
            'align' => 'left',
            'type' => 'options',
            'options' => array(
                Amasty_Audit_Model_Data::UNSUCCESS => $this->__('Failed'),
                Amasty_Audit_Model_Data::SUCCESS => $this->__('Success'),
                Amasty_Audit_Model_Data::LOCKED => $this->__('Locked out'),
                Amasty_Audit_Model_Data::LOGOUT => $this->__('Logout')),
        ));

        return parent::_prepareColumns();
    }

    private function getStoreOptions()
    {
        $array = Mage::app()->getStores(true);
        $options = array();
        foreach ($array as $key => $value) {
            $options[$key] = $value->getName();
        }
        return $options;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}