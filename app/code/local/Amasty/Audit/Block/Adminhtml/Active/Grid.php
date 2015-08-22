<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Block_Adminhtml_Active_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('recent_activity');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amaudit/active')->getCollection();
        $collection->getSize();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $hlp = Mage::helper('amaudit');

        $this->addColumn('username', array(
            'header'         => $hlp->__('Username'),
            'index'          => 'username',
        ))
        ;

        $this->addColumn('name', array(
            'header'         => $hlp->__('Full Name'),
            'index'          => 'name',
        ))
        ;

        $this->addColumn('date_time', array(
            'header'         => $hlp->__('Logged In At'),
            'index'          => 'date_time',
            'type'           => 'datetime'
        ))
        ;

        $this->addColumn('ip', array(
            'header'         => $hlp->__('IP Address'),
            'index'          => 'ip',
        ))
        ;

        $this->addColumn('location', array(
            'header'         => $hlp->__('Location'),
            'index'          => 'location',
        ))
        ;

        $this->addColumn('recent_activity', array(
            'header'         => $hlp->__('Recent Activity'),
            'index'          => 'recent_activity',
            'frame_callback' => array($this, 'decorateRecentActivity')
        ))
        ;

        $link= Mage::helper('adminhtml')->getUrl('amaudit/adminhtml_active/terminate') .'session_id/$session_id';
        $this->addColumn('action', array(
            'header'   => $hlp->__('Actions'),
            'sortable' => false,
            'filter'   => false,
            'type'     => 'action',
            'actions'  => array(
                array(
                    'url'     => $link,
                    'caption' => $hlp->__('Terminate Session'),
                    'confirm' => $hlp->__(
                        'Are you sure?'
                    ),
                ),
            ),
        ));

        return parent::_prepareColumns();
    }

    public function decorateRecentActivity($currentTimeStamp)
    {
        $_minute = 60;
        $_hour = 3600;
        $_3hours = 10800;
        $hlp = Mage::helper('amaudit');

        $currentTime = Mage::getModel('core/date')->timestamp(time());
        $rowTime = strtotime($currentTimeStamp);
        $timeDifference = $currentTime - $rowTime;

        if ($timeDifference < $_minute) {
            return 'Just Now';
        } elseif ($timeDifference < $_hour) {
            $minutes = round($timeDifference / 60);
            return  $hlp->__("%d minute(s) ago", $minutes);
        } elseif ($timeDifference < $_3hours) {
            $hours = round($timeDifference / 3600);
            return $hlp->__("%d hour(s) ago", $hours);
        }

        return $currentTimeStamp;
    }
}
