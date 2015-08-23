<?php



class MW_FollowUpEmail_Adminhtml_Followupemail_ReportemailsController extends Mage_Adminhtml_Controller_Action

{



	protected function _initAction() {

		$this->loadLayout()

			->_setActiveMenu('followupemail/items')

			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

		

		return $this;

	}   

 

	public function indexAction() {
		$this->_title($this->__('Report Emails'));            

        $this->_initAction()
            ->_setActiveMenu('followupemail/reportemails')
            ->_addBreadcrumb(Mage::helper('followupemail')->__('Report Emails'), Mage::helper('followupemail')->__('Report Emails by time range'))
            ->_addContent($this->getLayout()->createBlock('followupemail/adminhtml_reportemails'))
            ->renderLayout();
	}

    public function exportEmailsCsvAction()

    {

        $fileName   = 'report_emails.csv';

        $content    = $this->getLayout()->createBlock('followupemail/adminhtml_reportemails_grid')

            ->getCsv();



        $this->_sendUploadResponse($fileName, $content);

    }



    public function exportEmailsExcelAction()

    {

        $fileName   = 'report_emails.xml';
        $content    = $this->getLayout()->createBlock('followupemail/adminhtml_reportemails_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);

    }



    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')

    {

        $response = $this->getResponse();

        $response->setHeader('HTTP/1.1 200 OK','');

        $response->setHeader('Pragma', 'public', true);

        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);

        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);

        $response->setHeader('Last-Modified', date('r'));

        $response->setHeader('Accept-Ranges', 'bytes');

        $response->setHeader('Content-Length', strlen($content));

        $response->setHeader('Content-type', $contentType);

        $response->setBody($content);

        $response->sendResponse();

        die;

    }

	

	public function gridAction()

    {

        $this->loadLayout();

        $this->getResponse()->setBody($this->getLayout()->createBlock('followupemail/adminhtml_queue_grid')->toHtml());

    }	

}