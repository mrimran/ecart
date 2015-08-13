<?php



class MW_FollowUpEmail_Adminhtml_Followupemail_CouponsController extends Mage_Adminhtml_Controller_Action

{



	protected function _initAction() {

		$this->loadLayout()

			->_setActiveMenu('followupemail/items')

			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Coupons'), Mage::helper('adminhtml')->__('Manage Coupons'));

		

		return $this;

	}


	public function indexAction() {		

		$this->_initAction()

			->renderLayout();

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

        $couponIds = $this->getRequest()->getParam('coupon');

        if(!is_array($couponIds)) {

			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));

        } else {

            try {

                foreach ($couponIds as $couponId) {

                    $coupon = Mage::getModel('followupemail/coupons')->load($couponId);

                    $coupon->delete();

                }

                Mage::getSingleton('adminhtml/session')->addSuccess(

                    Mage::helper('adminhtml')->__(

                        'Total of %d record(s) were successfully deleted', count($couponIds)

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

        $couponIds = $this->getRequest()->getParam('coupon');

        if(!is_array($couponIds)) {

            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));

        } else {

            try {

                foreach ($couponIds as $couponId) {

                    $rule = Mage::getSingleton('followupemail/coupons')

                        ->load($couponId)

                        ->setCouponStatus($this->getRequest()->getParam('coupon_status'))

                        ->setIsMassupdate(true)

                        ->save();

                }

                $this->_getSession()->addSuccess(

                    $this->__('Total of %d record(s) were successfully updated', count($couponIds))

                );

            } catch (Exception $e) {

                $this->_getSession()->addError($e->getMessage());

            }

        }

        $this->_redirect('*/*/index');

    }
	public function gridAction()

    {

        $this->loadLayout();

        $this->getResponse()->setBody($this->getLayout()->createBlock('followupemail/adminhtml_coupons_grid')->toHtml());

    }	

}