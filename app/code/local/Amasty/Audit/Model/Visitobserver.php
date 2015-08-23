<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */


class Amasty_Audit_Model_Visitobserver
{
    //listen controller_action_layout_render_before
    public function onPageLoad($observer)
    {
        $detailData = array();
        $blockHead = Mage::app()->getLayout()->getBlock('head');
        if (!$blockHead) {
            return;
        }
        $detailData['page_name'] = str_replace(" / Magento Admin", "", $blockHead->getTitle());;
        $sessionId = session_id();
        $detailData['page_url'] = Mage::helper('core/url')->getCurrentUrl();
        $detailData['session_id'] = $sessionId;
        $detailModel = Mage::getModel('amaudit/visit_detail');
        $detailModel->saveLastPageDuration($sessionId);
        Mage::getSingleton('core/session')->setLastPageTime(time());
        $detailModel->setData($detailData);
        $detailModel->save();
    }
}