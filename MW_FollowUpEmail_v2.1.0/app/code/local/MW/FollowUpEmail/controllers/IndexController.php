<?php
class MW_FollowUpEmail_IndexController extends Mage_Core_Controller_Front_Action

{

    public function indexAction()
    {
        $this->loadLayout();     
        $this->renderLayout();
    }

    public function checkStatusAction(){
        $id = $this->getRequest()->getParam('eid');        
        $model = Mage::getModel('followupemail/emailqueue');            
        $model->load($id);
        
        if($model->getCustomerResponse() != MW_FollowUpEmail_Model_System_Config_Response::QUEUE_STATUS_CLICKED && $model->getCustomerResponse() != MW_FollowUpEmail_Model_System_Config_Response::QUEUE_STATUS_PURCHASED){
            $model->setCustomerResponse(MW_FollowUpEmail_Model_System_Config_Response::QUEUE_STATUS_READ);
            $model->save();
        }
    }

    public function directAction(){

        if ($code = $this->getRequest()->getParam('code')) {

            $code = str_replace(' ','+',$code);                 
            $code = str_replace('special','/',$code);                 
            $value = MW_FollowUpEmail_Helper_Data::decryptCode($code);                           
            $pos = strrpos($value, ",");
            if ($pos === false) { // note: three equal signs

                if (!$queue = Mage::getModel('followupemail/emailqueue')->loadByCode($code)) {

                    Mage::getSingleton('core/session')->addError($this->__('Wrong direct code specified'));

                    $this->_redirect('/');

                    return;

                }

                $customer = Mage::getModel('customer/customer')

                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())

                    ->loadByEmail($queue->getRecipientEmail());

            



                if ($customerId = $customer->getId()) {

                    $session = Mage::getSingleton('customer/session');

                    if ($session->isLoggedIn() && $customerId != $session->getCustomerId())

                        $session->logout();



                    try {

                        $session->setCustomerAsLoggedIn($customer);

                    } catch (Exception $ex) {

                        Mage::getSingleton('core/session')->addError($this->__('Your account isn\'t confirmed'));

                        $this->_redirect('/');

                    }

                }
                else{                                   
                    
                    $paramsV = unserialize($queue->getParams());
                    
                    Mage::getSingleton('checkout/session')->setQuoteId($paramsV['cart']['quote_id']);                
                    Mage::getSingleton('core/session')->setEmailGuest($paramsV['cart']['customer_email']);
                }            

                if($queue->getOrderId() > 0)

                    $this->getResponse()->setRedirect(Mage::getUrl("sales/order/view", array('order_id'=>$queue->getOrderId())));

                else {

                    $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
                    Mage::getModel('followupemail/observer')->eventDeleteQueueSpecial(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::EVENT_TYPE_CART_UPDATED,$paramsV['cart']['customer_email'],"",$groupId = 0,$storeId = 0,1);
                }

            }

            else{                
                
                $arrValue = explode(',',$value);

                $customer = Mage::getModel('customer/customer')

                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())

                ->loadByEmail($arrValue[0]);

                if ($customerId = $customer->getId()) {

                    $session = Mage::getSingleton('customer/session');

                    if ($session->isLoggedIn() && $customerId != $session->getCustomerId())

                        $session->logout();



                    try {

                        $session->setCustomerAsLoggedIn($customer);

                    } catch (Exception $ex) {

                        Mage::getSingleton('core/session')->addError($this->__('Your account isn\'t confirmed'));

                        $this->_redirect('/');

                    }

                }
                else{
                    if (!$queue = Mage::getModel('followupemail/emailqueue')->loadByCode($code)) {

                    Mage::getSingleton('core/session')->addError($this->__('Wrong direct code specified'));

                    $this->_redirect('/');

                    return;

                    }                
                    
                    $paramsV = unserialize($queue->getParams());
                    //print_r($paramsV['cart']);die;
                    //print_r($paramsV['cart']['quote_id']);die;
                    Mage::getSingleton('checkout/session')->setQuoteId($paramsV['cart']['quote_id']);
                    Mage::getSingleton('core/session')->setEmailGuest($paramsV['cart']['customer_email']);
                    Mage::getModel('followupemail/observer')->eventDeleteQueueSpecial(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::EVENT_TYPE_CART_UPDATED,$paramsV['cart']['customer_email'],"",$groupId = 0,$storeId = 0,1);
                }            

                if($arrValue[1] == 'order')

                    $this->getResponse()->setRedirect(Mage::getUrl("sales/order/view", array('order_id'=>$arrValue[2])));            

                else                

                    $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));    

            }              

        }

        else
        {
            Mage::getSingleton('core/session')->addError($this->__('No resume code cpecified'));
            $this->_redirect('/');
        }
    }

    public function successAction(){
//        Mage::getModel('followupemail/emailqueue')->load(7)->send();
        $code = $this->getRequest()->getParam('code');
        if($code){
            $email = base64_decode($code);
            Mage::getModel('followupemail/emailqueue')->ununsubscribe($email);
        }
        $this->loadLayout();
        $this->renderLayout();
    }
}