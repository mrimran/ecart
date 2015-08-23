<?php



class MW_FollowUpEmail_Adminhtml_Followupemail_RulesController extends Mage_Adminhtml_Controller_Action

{



	protected function _initAction() {

		$this->loadLayout()

			->_setActiveMenu('followupemail/items')

			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

		

		return $this;

	}

	

	public function testAction(){

		$result['mess'] = '';

		$result['err'] = 0;

		$model = Mage::getModel('followupemail/rules');

		$data = $this->getRequest()->getPost();				

		$model->load($this->getRequest()->getParam('id'));		

        $model->loadPost($data);

        if(!$data['testemail']['test_recipient'])

        {                

            $result['mess'] = 'To send a test message you have to fill up the \'Test recipient\' field';

            $result['err'] = 1;

        }

        if($model->sendTestEmail($data)){

			$result['mess'] = 'Test email was successfully sent';                

		}			

        else{

			$result['mess'] = 'Error sending test message';               

			$result['err'] = 1;

		}				        

		$this->getResponse()->setHeader('Content-Type', 'application/json', true)->setBody(json_encode($result));

	}
	
	public function applyoldbackdataAction(){		
		$result['mess'] = '';
		$result['err'] = 0;
		$data = $this->getRequest()->getPost();				
		$model = Mage::getModel('followupemail/applyoldbackdata');			
		$rule = Mage::getModel('followupemail/rules')->load($data['rule_id']);	
		if($model->Eventoldback($rule)){
			$result['mess'] = "Email queues have been applied for the old data.";
		}	
		else{
			$result['mess'] = "Create the old data of the event was error";
			$result['err'] = 1;
		}
		$this->getResponse()->setHeader('Content-Type', 'application/json', true)->setBody(json_encode($result));
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
			Mage::register('current_fue_rule', $model);
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

 

	public function saveAction() {

		if ($data = $this->getRequest()->getPost()) {

			$model = Mage::getModel('followupemail/rules');

			//$model->setData($data)

				//->setId($this->getRequest()->getParam('id'));					

			$session = Mage::getSingleton('adminhtml/session');

			try {

				$id = $this->getRequest()->getParam('rule_id');

                if ($id) {

                    $model->load($id);

                    if ($id != $model->getId()) {

                        Mage::throwException(Mage::helper('followupemail')->__('Wrong rule specified.'));

                    }

                }

				

				$data = $this->_filterDates($data, array(

                    'from_date',

                    'to_date'

                ));

				

				// save conditions

				if (isset($data['rule']['conditions'])) {

	                $data['conditions'] = $data['rule']['conditions'];

	            }

				

				$model->load($this->getRequest()->getParam('id'));

				unset($data['rule']);

				

				//if (!$data['from_date'])

                    //$data['from_date'] = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));

                $model->loadPost($data);

				

				$model->setData('from_date', $data['from_date']);

                $model->setData('to_date', $data['to_date']);

				

				$store_ids = "";

				$customer_group_ids = "";

				$cancel_events = "";				

				$email_chain = "";				

               	

				if (isset($data["store_ids"])) {

                    $store_ids = implode(",", $data["store_ids"]);

                }				

                if (isset($data["customer_group_ids"])) {

                    $customer_group_ids = implode(",", $data["customer_group_ids"]);

                }

				if (isset($data["cancel_event"])) {

                    $cancel_events = implode(",", $data["cancel_event"]);

                }

				$cancel_events = $cancel_events.',';

					

				// chain processing

	            if(!isset($data['email_chain'])) $data['email_chain'] = array();

	            else

	            {

	                foreach($data['email_chain'] as $key => $value)

	                {

	                    if(isset($value['delete']))

	                    {

	                        if($value['delete']) unset($data['email_chain'][$key]);

	                        else unset($data['email_chain'][$key]['delete']);

	                    }

	                }

	                foreach ($data['email_chain'] as $key => $value)

	                    if(false === strpos($value['TEMPLATE_ID'], MW_FollowUpEmail_Model_System_Config_Emailtemplate::TEMPLATE_SOURCE_SEPARATOR))

	                    {

	                        $session->addError($this->__('Please select template'));

	                        $session->setFollowupemailData($data);

	                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'tab' => 'conditions_section'));

	                        return;

	                    }



	                foreach($data['email_chain'] as $k => $v)

	                {												

	                    $data['email_chain'][$k]['DAYS'] = trim($data['email_chain'][$k]['DAYS']);

	                    if($data['email_chain'][$k]['DAYS'] && !is_numeric($data['email_chain'][$k]['DAYS']))

	                    {

	                        $session->addError($this->__('The quantity of days in the chain is not a number'));

	                        $session->setFollowupemailData($data);

	                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'tab' => 'conditions_section'));

	                        return;

	                    }

	                }



	                if(count($data['email_chain']) > 0)

	                {

	                    // sorting						

	                    $chainSorted = array();

	                    foreach($data['email_chain'] as $k => $v)

	                        $chainSorted[$v['BEFORE']*($v['DAYS']*1440+$v['HOURS']*60+$v['MINUTES'])*10000 + mt_rand(0,9999)] = $k;



	                    ksort($chainSorted, SORT_NUMERIC);



	                    $chain = array();

	                    foreach($chainSorted as $k => $v)

	                        $chain[] = $data['email_chain'][$v];



	                    $email_chain = $chain;

	                }

	            }		
				
				if($data['coupon_status'] == 1 && $data['coupon_sales_rule_id'] == ''){
					$session->addError($this->__('Please select shopping cart rule'));

                    $session->setFollowupemailData($data);

                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'tab' => 'coupons_section'));

                    return;
				}		

				$model->setData('customer_group_ids', $customer_group_ids);

                $model->setData('cancel_event', $cancel_events);

                $model->setData('store_ids', $store_ids);

                $model->setData('email_chain', serialize($email_chain));

				

				

				//Save data sendtest

				$model->setData('test_recipient', $data['testemail']['test_recipient']);                

                $model->setData('test_customer_name', $data['testemail']['test_customer_name']);

                $model->setData('test_order_id', $data['testemail']['test_order_id']);                

				

                /*if (isset($data["website_ids"])) {

                    $website_ids = implode(",", $data["website_ids"]);

                }*/

				

				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('followupemail')->__('Item was successfully saved'));

				Mage::getSingleton('adminhtml/session')->setFormData(false);



				if ($this->getRequest()->getParam('back')) {

					$this->_redirect('*/*/edit', array('id' => $model->getId()));

					return;

				}

				$this->_redirect('*/*/');

				return;

            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                Mage::getSingleton('adminhtml/session')->setFormData($data);

                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

                return;

            }

        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('followupemail')->__('Unable to find item to save'));

        $this->_redirect('*/*/');

	}

 

	public function deleteAction() {

		if( $this->getRequest()->getParam('id') > 0 ) {

			try {

				$model = Mage::getModel('followupemail/rules');

				 

				$model->setId($this->getRequest()->getParam('id'))

					->delete();

				$queue = Mage::getModel('followupemail/emailqueue');		           				

					$queueEmails = $queue->getCollection()

						->addFieldToFilter('rule_id', $this->getRequest()->getParam('id'));

						//->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);							

					$queueEmails->load();

					

					foreach($queueEmails->getData() as $queueEmail){							

						 $deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);							 

						 $deleteQueue->delete();

					}	 

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));

				$this->_redirect('*/*/');

			} catch (Exception $e) {

				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

			}

		}

		$this->_redirect('*/*/');

	}



    public function massDeleteAction() {

        $ruleIds = $this->getRequest()->getParam('followupemail');

        if(!is_array($ruleIds)) {

			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));

        } else {

            try {

                foreach ($ruleIds as $ruleId) {

                    $rule = Mage::getModel('followupemail/rules')->load($ruleId);

                    $rule->delete();
					
					$queue = Mage::getModel('followupemail/emailqueue');		           				

					$queueEmails = $queue->getCollection()

						->addFieldToFilter('rule_id', $ruleId);

						//->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);							

					$queueEmails->load();

					

					foreach($queueEmails->getData() as $queueEmail){							

						 $deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);							 

						 $deleteQueue->delete();

					}

                }

                Mage::getSingleton('adminhtml/session')->addSuccess(

                    Mage::helper('adminhtml')->__(

                        'Total of %d record(s) were successfully deleted', count($ruleIds)

                    )

                );

            } catch (Exception $e) {

                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

            }

        }

        $this->_redirect('*/*/index');

    }

	

    public function massStatusAction()

    {

        $ruleIds = $this->getRequest()->getParam('followupemail');

        if(!is_array($ruleIds)) {

            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));

        } else {

            try {

                foreach ($ruleIds as $ruleId) {

                    $rule = Mage::getSingleton('followupemail/rules')

                        ->load($ruleId)

                        ->setIsActive($this->getRequest()->getParam('is_active'))

                        ->setIsMassupdate(true)

                        ->save();

                }

                $this->_getSession()->addSuccess(

                    $this->__('Total of %d record(s) were successfully updated', count($ruleIds))

                );

            } catch (Exception $e) {

                $this->_getSession()->addError($e->getMessage());

            }

        }

        $this->_redirect('*/*/index');

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

}