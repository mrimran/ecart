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
    public function latestcategoryproductAjaxAction() 
    {
        $block = $this->getLayout()->createBlock('extension/category')
        ->setTemplate('catalog/category/latestproduct_category.phtml');
         $this->getResponse()->setBody($block->toHtml());
    }

    public function brand_categoryAjaxAction() 
    {
        $block = $this->getLayout()->createBlock('extension/category')
        ->setTemplate('catalog/category/brand_categoryAjax.phtml');
         $this->getResponse()->setBody($block->toHtml());
    }
}

?>