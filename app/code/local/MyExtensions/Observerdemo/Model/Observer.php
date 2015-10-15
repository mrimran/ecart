<?php
 
class MyExtensions_Observerdemo_Model_Observer {
 
    public function addtocartEvent(Varien_Event_Observer $observer) {
 echo 'asdf'; exit;
        $event = $observer->getEvent();  //Gets the event
        $product = $event->getProduct();
        $observermsg = "The event triggered>>> <B>" . $event->getName() . "</B><br/> The added product>>> <B> " . $product->getName()."</B>";
        //Adds the message to the shopping cart
        echo Mage::getSingleton('checkout/session')->addSuccess($observermsg);
    }
     
    public function autoApproveReview(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $reviewObjData = $event->getData();
        //print_r($reviewObjData);exit;
 
        $reviewData=$reviewObjData["data_object"];
        $reviewData->setStatusId(Mage_Review_Model_Review::STATUS_APPROVED);
         
        echo Mage::getSingleton('core/session')->addSuccess("Thank you for your input!!");
    }
}