<?php



class MW_FollowUpEmail_Adminhtml_Followupemail_QueueController extends Mage_Adminhtml_Controller_Action

{



	protected function _initAction() {

		$this->loadLayout()

			->_setActiveMenu('followupemail/items')

			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

		

		return $this;

	}   

 

	public function indexAction() {		

		$this->_initAction()

			->renderLayout();

	}



	public function editAction() {

		$id     = $this->getRequest()->getParam('id');

		$model  = Mage::getModel('followupemail/rules')->load($id);

		$data = $model->getData();

		if ($model->getId() || $id == 0) {

			//$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

			$sessionData = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if(is_array($sessionData)) $data = array_merge($data, $sessionData);            

						

			if(!isset($data['email_chain'])) $data['email_chain'] = array();

            elseif(!is_array($data['email_chain'])) $data['email_chain'] = @unserialize($data['email_chain']);

			if (!empty($data)) {

				$model->setData($data);

			}			

			Mage::getModel('followupemail/rules')->getConditions()->setJsFormObject('rule_conditions_fieldset');

			Mage::getModel('followupemail/rules')->getActions()->setJsFormObject('rule_actions_fieldset');			

			Mage::register('rules_data', $model);



			$this->loadLayout();

			$this->_setActiveMenu('followupemail/items');



			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));



			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);



			/*$this->_addContent($this->getLayout()->createBlock('followupemail/adminhtml_rules_edit'))

				->_addLeft($this->getLayout()->createBlock('followupemail/adminhtml_rules_edit_tabs'));*/



			$this->renderLayout();

		} else {

			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('followupemail')->__('Item does not exist'));

			$this->_redirect('*/*/');

		}

	}

 

	public function newAction() {

		$this->_forward('edit');

	}

	

	public function updateAction()

    {

    }

	

	private function deleteEmail($id, $showMessage = true)

    {		

        //Mage::getModel('followupemail/emailqueue')->setId($id)->delete();        
        Mage::getModel('followupemail/emailqueue')->setId($id)->setStatus(5)->save();        

        if($showMessage) {

            $message = $this->__('Email was successfully deleted');

            Mage::getSingleton('adminhtml/session')->addSuccess($message);

        }

    }



    private function cancelEmail($id, $showMessage = true)

    {		

        Mage::getModel('followupemail/emailqueue')->load($id)->cancel();

        //Mage::getSingleton('followupemail/log')->logSuccess("email id=$id cancelled by Administrator", $this);

        if($showMessage) {

            $message = $this->__('Email was successfully cancelled');

            Mage::getSingleton('adminhtml/session')->addSuccess($message);

        }

    }



    private function sendEmail($id, $showMessage = true) {	

		$emailBefore = Mage::getModel('followupemail/emailqueue')->load($id);			

		// email content

		if (@unserialize($emailBefore->getParams()) === FALSE ){

			Mage::getModel('followupemail/observer')->updateParamsEmail($emailBefore);

		}		

        $result = Mage::getModel('followupemail/emailqueue')->load($id)->send();

        if($result === TRUE) {

            //Mage::getSingleton('followupemail/log')->logSuccess("email id=$id sent by Administrator", $this);

            if($showMessage) {

                $message = $this->__('Email was successfully sent');

                Mage::getSingleton('adminhtml/session')->addSuccess($message);

            }

        }
		
		else if($result == 3){

            //Mage::getSingleton('followupemail/log')->logError("email id=$id could not be sent by Administrator", $this);

            if($showMessage) {

                $message = $this->__('This email is not sent to customer neither BBCed to anyone');

                Mage::getSingleton('adminhtml/session')->addError($message);

            }

        }
		
        else {

            //Mage::getSingleton('followupemail/log')->logError("email id=$id could not be sent by Administrator", $this);

            if($showMessage) {

                $message = $this->__('Could not send the email');

                Mage::getSingleton('adminhtml/session')->addError($message);

            }

        }

    }

 

	public function deleteAction() {

		if( $this->getRequest()->getParam('queue_id') > 0 ) {

			try {

				$model = Mage::getModel('followupemail/emailqueue');

				 

				$model->setId($this->getRequest()->getParam('queue_id'))

					->setStatus(5)->save();

					 

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));

				$this->_redirect('*/*/');

			} catch (Exception $e) {

				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

				//$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('queue_id')));

			}

		}

		$this->_redirect('*/*/');

	}

	

	public function massactionSendAction() {

        $emailIds = $this->getRequest()->getParam('emailqueue');

        $cnt = 0;

        if(is_array($emailIds)) {

            foreach($emailIds as $emailId) {

                $this->sendEmail($emailId, false);

                $cnt++;

            }

            if($cnt) {

                Mage::getSingleton('adminhtml/session')->addSuccess(

                    Mage::helper('followupemail')->__('Total of %d record(s) were successfully sent', $cnt)

                );

            }

        }

        return $this->_redirect('*/*/index');

    }



    public function massactionCancelAction() {

        $emailIds = $this->getRequest()->getParam('emailqueue');

        $cnt = 0;

        if(is_array($emailIds)) {

            foreach($emailIds as $emailId) {

                $this->cancelEmail($emailId, false);

                $cnt++;

            }

            if($cnt) {

                Mage::getSingleton('adminhtml/session')->addSuccess(

                    Mage::helper('followupemail')->__('Total of %d record(s) were successfully cancelled', $cnt)

                );

            }

        }

        return $this->_redirect('*/*/index');

    }



    public function massactionDeleteAction() {

        $emailIds = $this->getRequest()->getParam('emailqueue');		

        $cnt = 0;

        if(is_array($emailIds)) {

            foreach($emailIds as $emailId) {

                $this->deleteEmail($emailId, false);

                $cnt++;

            }

            if($cnt) {

                Mage::getSingleton('adminhtml/session')->addSuccess(

                    Mage::helper('followupemail')->__('Total of %d record(s) were successfully deleted', $cnt)

                );

            }

        }

        return $this->_redirect('*/*/index');

    }    

  

    public function exportCsvAction()

    {

        $fileName   = 'rule.csv';

        $content    = $this->getLayout()->createBlock('followupemail/adminhtml_rules_grid')

            ->getCsv();



        $this->_sendUploadResponse($fileName, $content);

    }



    public function exportXmlAction()

    {

        $fileName   = 'rule.xml';

        $content    = $this->getLayout()->createBlock('followupemail/adminhtml_rules_grid')

            ->getXml();



        $this->_sendUploadResponse($fileName, $content);

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

	

	public function previewAction()

    {

        if($id = $this->getRequest()->getParam('queue_id'))

        {

            $emailBefore = Mage::getModel('followupemail/emailqueue')->load($id);						

			// email content			

			if (@unserialize($emailBefore->getParams()) === FALSE && $emailBefore->getStatus() == 1){

				Mage::getModel('followupemail/observer')->updateParamsEmail($emailBefore);

			}			

			$email = Mage::getModel('followupemail/emailqueue')->load($id);			

			//mage::log(unserialize($email->getParams()));

			$content = $email->getContent();

			if($emailBefore->getStatus() != 2)			

			$content = Mage::helper('followupemail')->_prepareContentEmail(unserialize($email->getParams()),$id,true);
			
			
			$subject = Mage::helper('followupemail')->_prepareSubjectEmail(unserialize($email->getParams()),$email->getSubject());
            if(!$email->getId())

            {

                Mage::getSingleton('adminhtml/session')->addError($this->__('Email does not longer exist'));

                $this->_redirect('*/*/');

                return;

            }

            $rule = Mage::getModel('followupemail/rules')->load($email->getRuleId());



            $from = Mage::getResourceModel('followupemail/rules')->getAllEmailRule($email->getRuleId());

            if(!isset($from['send_mail_customer'])) $from['send_mail_customer'] = true;           

			

 			$template = Mage::getModel('core/email_template');

			$template->setTemplateText($content);		

			/* @var $filter Mage_Core_Model_Input_Filter_MaliciousCode */

	        $filter = Mage::getSingleton('core/input_filter_maliciousCode');



	        $template->setTemplateText(

	            $filter->filter($template->getTemplateText())

	        );



	        Varien_Profiler::start("email_template_proccessing");

	        $vars = array();



	        $templateProcessed = $template->getProcessedTemplate($vars, true);



	        if ($template->isPlain()) {

	            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";

	        }



	        Varien_Profiler::stop("email_template_proccessing");



	        //return $templateProcessed;

            $this->getResponse()->setBody(

                $this->getLayout()->createBlock('core/template')

                    ->setId($id)

                    ->setTemplate('mw_followupemail/preview.phtml')

                    ->setSenderName($email->getSenderName())

                    ->setSenderEmail($email->getSenderEmail())

                    ->setRecipientName($email->getRecipientName())

                    ->setRecipientEmail($email->getRecipientEmail())

                    ->setSubject($subject)

                    ->setEmailCopyTo(isset($_emailCopyTo) ? $_emailCopyTo : $rule->getEmailCopyTo())

                    ->setContent($templateProcessed)

                    ->setStatus($email->getStatus())

                    ->toHtml());

        }

    }

	

	public function cancelAction()

    {

        if($id = $this->getRequest()->getParam('queue_id'))

            try

            {

                $this->cancelEmail($id);

            }

            catch (Exception $e)

            {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());                

            }

        $this->_redirect('*/*/');

    }



    public function sendAction() {

        if($id = $this->getRequest()->getParam('queue_id')) {

            try {

                $this->sendEmail($id);

            }

            catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

            }

        }

        $this->_redirect('*/*/');

    }	

}