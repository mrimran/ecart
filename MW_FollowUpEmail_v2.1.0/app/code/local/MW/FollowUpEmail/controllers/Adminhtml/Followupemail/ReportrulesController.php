<?php



class MW_FollowUpEmail_Adminhtml_Followupemail_ReportrulesController extends Mage_Adminhtml_Controller_Action

{



	protected function _initAction() {

		$this->loadLayout()

			->_setActiveMenu('followupemail/items')

			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

		

		return $this;
        
	}   

 

	public function indexAction() {
		$this->_title($this->__('Report Rules'));            

        $this->_initAction()
            ->_setActiveMenu('followupemail/reportrules')
            ->_addBreadcrumb(Mage::helper('followupemail')->__('Report Rules'), Mage::helper('followupemail')->__('Report Rules by time range'))
            ->_addContent($this->getLayout()->createBlock('followupemail/adminhtml_reportrules'))
            ->renderLayout();
	}

    public function exportRulesCsvAction()
    {
        $fileName   = 'report_rules.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_reportrules_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportRulesExcelAction()
    {
        $fileName   = 'report_rules.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_reportrules_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }


	public function gridAction()

    {

        $this->loadLayout();

        $this->getResponse()->setBody($this->getLayout()->createBlock('followupemail/adminhtml_queue_grid')->toHtml());

    }	

}