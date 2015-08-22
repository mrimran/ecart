<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Block_Adminhtml_Userlog_Edit_Tab_View_Popup extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amaudit/tab/view/details_popup.phtml');
    }

    public function getRestoreUrl()
    {
        $id = $this->getRequest()->getParam('id');
        $url = $this->getUrl('amaudit/adminhtml_log/restore', array('id' => $id));
        return $url;
    }
}
