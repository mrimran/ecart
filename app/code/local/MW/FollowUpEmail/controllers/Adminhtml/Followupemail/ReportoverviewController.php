<?php
class MW_FollowUpEmail_Adminhtml_Followupemail_ReportoverviewController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			 ->_setActiveMenu('followupemail/items')
			 ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

			 return $this;	
	}

	public function indexAction(){
        if($this->getRequest()->getPost('ajax') =='true'){
            $data = $this->getRequest()->getPost();
            print Mage::getModel('followupemail/report')->prepareCollection($data);
            exit();
        }

		$this->_title($this->__("Reports"))
			->_title($this->__("Result"))
			->_title($this->__('Overview'));

		$this->loadLayout()
			 ->_addContent($this->getLayout()->createBlock('followupemail/adminhtml_reportoverview'));
		$this->renderLayout();
	}
}