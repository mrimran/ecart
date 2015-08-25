<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Model_Mailsender
{
    public function sendMail($data, $type, $mail)
    {
        switch ($type) {
            case 'success':
                $template = Mage::getStoreConfig('amaudit/log_mailing/template');
                break;
            case 'unsuccessful':
                $template = Mage::getStoreConfig('amaudit/unsuccessful_log_mailing/template');
                break;
            case 'suspicious':
                $template = Mage::getStoreConfig('amaudit/suspicious_log_mailing/template');
                break;
        }
        //template use recipient name without 'fullname'
        $data['fullname'] = isset($data['name']) ? $data['name'] : NULL;
        $store = Mage::app()->getStore();
        $sender = array(
            'name' => Mage::getStoreConfig('trans_email/ident_general/name', Mage::app()->getStore()->getId()),
            'email' => Mage::getStoreConfig('trans_email/ident_general/email', Mage::app()->getStore()->getId()),
        );
        if (isset($template)) {
            $tpl = Mage::getModel('core/email_template');
            $tpl->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()))
                ->sendTransactional(
                    $template,
                    $sender,
                    $mail,
                    $mail,
                    $data
                );
        }

    }
}