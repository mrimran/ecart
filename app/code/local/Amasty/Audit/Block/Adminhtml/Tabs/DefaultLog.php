<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */


abstract class Amasty_Audit_Block_Adminhtml_Tabs_DefaultLog extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('date_time');
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

    public function decorateStatus($value, $row, $column)
    {
        return '<span class="amaudit-' . $value . '">' . $value . '</span>';
    }


    public function showOpenElementUrl($value, $row, $column)
    {
        $category = $row->getCategory();
        $url = '';
        $mass = explode('/', $category);
        $param = ($row->getParametrName() == "back" || $row->getParametrName() == "underfined") ? "id" : $row->getParametrName();
        if ($row->getElementId() && $category && $row->getType() != "Delete" && array_key_exists(1, $mass) && ($mass[1] == "catalog_product" || $mass[1] == "customer" || $mass[1] == "customer_group" || $mass[1] == "catalog_product_attribute")) {
            $params = array($mass[1] => $row->getElementId());
            $url = $this->getUrl('adminhtml/' . $mass[1] . '/edit', array($param => $row->getElementId()));
        }

        $info = $row->getInfo();
        if (strpos($info, "Order ID") !== false) {
            $url = $this->getUrl('adminhtml/sales_order/view', array('order_id' =>  preg_replace("/[^0-9]/", '', $info)));
        }
        $view = "";
        if ($url) $view = '&nbsp<a href="' . $url . '"><span>[' . Mage::helper('amaudit')->__('view') . ']</span></a>';

        return '<span>' . $value . '</span>' . $view;
    }

    public function showActions($value, $row, $column)
    {
        $preview = "";

        if (($row->getType() == "Edit" || $row->getType() == "New" || $row->getType() == 'Restore') && ($row->is_logged != NULL)) $preview = '<a class="amaudit-preview" id="' . $row->getId() . '" onclick="buble.showToolTip(this); return false">' . Mage::helper('amaudit')->__('Preview Changes') . '</a><br>';

        return $preview . '<a href="' . $this->getUrl('amaudit/adminhtml_log/edit', array('id' => $row->getId())) . '"><span>' . Mage::helper('amaudit')->__('View Details') . '</span></a>';
    }

    public function showFullName($value, $row, $column)
    {
        $username = $row->getUsername();
        if ($username) {
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