<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */


class Amasty_Audit_Block_Adminhtml_Visit_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('session_start');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amaudit/visit')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $hlp = Mage::helper('amaudit');

        $this->addColumn('username', array(
            'header' => $hlp->__('Username'),
            'index' => 'username',
        ));

        $this->addColumn('name', array(
            'header' => $hlp->__('Full Name'),
            'index' => 'name',
        ));

        $this->addColumn('session_start', array(
            'header' => $hlp->__('Session Start'),
            'index' => 'session_start',
            'type' => 'datetime'
        ));

        $this->addColumn('session_end', array(
            'header' => $hlp->__('Session End'),
            'index' => 'session_end',
            'type' => 'datetime'
        ));

        $this->addColumn('ip', array(
            'header' => $hlp->__('Ip Address'),
            'index' => 'ip',
        ));

        $this->addColumn('location', array(
            'header' => $hlp->__('Location'),
            'index' => 'location',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
