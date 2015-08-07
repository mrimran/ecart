<?php
class Tabs_Extension_CategoryController extends Mage_Core_Controller_Front_Action{

    public function indexAction() 
       {
    	
    	   echo "Hello tuts+ World"; 
       }

    public function sellerAction() 
       {
    	
    	   $this->loadLayout();
           $this->renderLayout();
       }

    public function saleAction() 
       {
    	
    	   $this->loadLayout();
           $this->renderLayout();
       }
    
    public function latestAction() 
       {
    	
    	   $this->loadLayout();
           $this->renderLayout();
       }

    public function upcomingAction() 
       {
    	
    	   $this->loadLayout();
           $this->renderLayout();
       }
}

?>