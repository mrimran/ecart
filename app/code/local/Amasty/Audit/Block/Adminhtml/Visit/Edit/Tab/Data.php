<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */


class Amasty_Audit_Block_Adminhtml_Visit_Edit_Tab_Data  extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amaudit/tab/data.phtml');
    }

    public function getLog()
    {
        $log = Mage::registry('current_session_history');
        $sessionStart = strtotime($log->getSessionStart());
        $sessionEnd = strtotime($log->getSessionEnd());
        if (!empty($sessionStart)) {
            $log->setSessionStart(Mage::getModel('core/date')->date(null, $sessionStart));
        }
        if (!empty($sessionEnd)) {
            $log->setSessionEnd(Mage::getModel('core/date')->date(null, $sessionEnd));
        }
        return Mage::registry('current_session_history');
    }
}
