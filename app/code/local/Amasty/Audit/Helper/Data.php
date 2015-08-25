<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Helper_Data extends Mage_Core_Helper_Url
{
    public function getCatNameFromArray($name)
    {
        $adminPath = Mage::registry('amaudit_admin_path') ? Mage::registry('amaudit_admin_path') : 'admin';
        $nameArray = array(
            'amorderattr/adminhtml_order' => $this->__('Amasty Order Attribute'),
            'ampgrid/adminhtml_field' => $this->__('Amasty Product Grid'),
            $adminPath . '/sales_order' => $this->__('Order'),
            $adminPath . '/sales_order_edit ' => $this->__('Order'),
            $adminPath . '/catalog_product' => $this->__('Product'),
            $adminPath . '/catalog_product_attribute' => $this->__('Product Attribute'),
            $adminPath . '/catalog_product_set' => $this->__('Product Attribute Set'),
            $adminPath . '/tax_rule' => $this->__('Tax Rules'),
            $adminPath . '/tag' => $this->__('Tags'),
            $adminPath . '/rating' => $this->__('Rating'),
            $adminPath . '/customer_group' => $this->__('Customer Groups'),
            $adminPath . '/promo_catalog' => $this->__('Catalog Price Rules'),
            $adminPath . '/promo_quote' => $this->__('Shopping Cart Price Rules'),
            $adminPath . '/newsletter_template' => $this->__('Newsletter Templates'),
            $adminPath . '/cms_page' => $this->__('CMS Manage Pages'),
            $adminPath . '/cms_block' => $this->__('CMS Static Blocks'),
            $adminPath . '/widget_instance' => $this->__('CMS Widget Instances'),
            $adminPath . '/poll' => $this->__('CMS Poll'),
            $adminPath . '/system_config' => $this->__('System Configuration'),
            $adminPath . '/permissions_user' => $this->__('User'),
            $adminPath . '/permissions_role' => $this->__('Role'),
            $adminPath . '/system_design' => $this->__('System Design'),
            $adminPath . '/api_user' => $this->__('System Web Services Users'),
            $adminPath . '/api_role' => $this->__('System Web Services Roles'),
            $adminPath . '/system_email_template' => $this->__('System Transactional Emails'),
            $adminPath . '/system_variable' => $this->__('System Custom Variable'),
            $adminPath . '/catalog_category' => $this->__('Categories'),
            $adminPath . '/sales_order_shipment' => $this->__('Shipment'),
            $adminPath . '/sales_order_invoice' => $this->__('Invoice'),
            $adminPath . '/sales_order_creditmemo' => $this->__('Creditmemo'),
            $adminPath . '/urlrewrite' => $this->__('URL Rewrite Management'),
            $adminPath . '/customer' => $this->__('Customer'),
            $adminPath . '/sales_order_create' => $this->__('Order'),
            $adminPath . '/tax_class' => $this->__('Tax Class'),
            $adminPath . '/tax_rate' => $this->__('Tax Rate'),
            $adminPath . '/checkout_agreement' => $this->__('Terms and Conditions'),
            $adminPath . '/notification' => $this->__('Notification'),
            $adminPath . '/catalog_search' => $this->__('Search Term'),
            'adminhtml_log' => $this->__('Actions Log'),
            'sales_creditmemo' => $this->__('Creditmemo'),
            'sales_shipment' => $this->__('Shipment'),
            'sales_invoice' => $this->__('Invoice'),
            'sales_order' => $this->__('Order'),
            'adminhtml_login' => $this->__('Login Attempts'),
            'tax_rate' => $this->__('Export Tax Rates'),
        );

        if (array_key_exists($name, $nameArray)) {
            $name = $nameArray[$name];
        } else {
            $name = ucfirst($name);
        }

        return $name;
    }

    public function getLockUser($idUser)
    {
        $lockUser = Mage::getModel('amaudit/lock')->load($idUser, 'user_id');
        if ($lockUser->hasData()) {
            return $lockUser;
        }
        return null;
    }

    public function isUserInLog($userId)
    {
        if (!Mage::getStoreConfig('amaudit/log/is_all_admins')) {
            $massId = Mage::getStoreConfig('amaudit/log/log_users');
            $massId = explode(',', $massId);
            if (in_array($userId, $massId)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }

    }

    public function getCacheParams($params)
    {
        $option = array();
        $cacheTypes = Mage::app()->getCacheInstance()->getTypes();
        foreach ($params as $key => $value) {
            if (array_key_exists($value, $cacheTypes)) {
                $option[$cacheTypes[$value]->getData('cache_type')] = $cacheTypes[$value]->getData('description');
            }
        }
        return $option;
    }

    public function getIndexParams($params)
    {
        $option = array();
        $collection = Mage::getResourceModel('index/process_collection');
        if (!is_array($params)) {
            $params = array($params);
        }
        foreach ($collection as $item) {
            if (in_array($item->getProcessId(), $params)) {
                $option[$item->getIndexer()->getName()] = $item->getIndexer()->getDescription();
            }
        }
        return $option;
    }

    public function getUsername($userId)
    {
        $model = Mage::getModel('admin/user');
        $model->load($userId);
        return $model->getUsername();
    }
}
