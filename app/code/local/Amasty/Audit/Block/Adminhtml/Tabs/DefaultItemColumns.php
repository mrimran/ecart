<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */


abstract class Amasty_Audit_Block_Adminhtml_Tabs_DefaultItemColumns extends Amasty_Audit_Block_Adminhtml_Tabs_DefaultLog
{
    protected function _prepareColumns()
    {
        $this->addExportType('*/*/exportCsv', Mage::helper('amaudit')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('amaudit')->__('XML'));

        $hlp = Mage::helper('amaudit');

        $this->addColumn('date_time', array(
            'header'    => $hlp->__('Date'),
            'index'     => 'date_time',
            'type'      => 'datetime',
            'width'     => '170px',
        ))
        ;

        $this->addColumn('username', array(
            'header' => $hlp->__('Username'),
            'index'  => 'username',
            'align'  => 'center',
        ))
        ;

        $this->addColumn('fullname', array(
            'header'         => $hlp->__('Full name'),
            'index'          => 'username',
            'align'          => 'center',
            'frame_callback' => array($this, 'showFullName'),
        ))
        ;

        $this->addColumn('type', array(
            'header'         => $hlp->__('Action Type'),
            'index'          => 'type',
            'align'          => 'center',
            'frame_callback' => array($this, 'decorateStatus'),
        ))
        ;

        $this->addColumn('action',
            array(
                'header'         => $hlp->__('Actions'),
                'width'          => '170px',
                'align'          => 'center',
                'filter'         => false,
                'sortable'       => false,
                'frame_callback' => array($this, 'showActions')
            ))
        ;

    }
}