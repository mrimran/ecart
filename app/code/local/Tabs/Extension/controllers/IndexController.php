<?php
class Tabs_Extension_IndexController extends Mage_Core_Controller_Front_Action{
    
    public function indexAction() {
      
	 echo "Hello tuts+ World"; 
	  
    }

    public function sellerAction() 
    {
    	
    	$this->loadLayout();
        $this->renderLayout();
    }

    public function newAction() 
    {
    	
    	$this->loadLayout();
        $this->renderLayout();
    }

    public function ourcollectionAction() 
    {
        
        $this->loadLayout();
        $this->renderLayout();
    }

    public function productcollectionAction() 
    {
        
        $this->loadLayout();
        $this->renderLayout();
    }

     public function allsaleAction() 
    {
        
        $this->loadLayout();
        $this->renderLayout();
    }

    public function TrendingAction() 
    {
    	
    	$this->loadLayout();
        $this->renderLayout();
    }

     public function relatedAction() 
    {
        
        $this->loadLayout();
        $this->renderLayout();
    }

    public function mostviewedAction() 
    {   
        $this->loadLayout();
        $this->renderLayout();
    }

     public function categoryAction() 
    {
        
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saleAction() 
    {
        
        $this->loadLayout();
        $this->renderLayout();
    }

    public function phoneAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
    public function computerAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ajaxbestsellerAction(){
       $block = $this->getLayout()->createBlock('extension/computer')
        ->setTemplate('catalog/product/computeraccessoriesAjax.phtml');
         $this->getResponse()->setBody($block->toHtml());

    }

     public function ajaxnewproductAction(){
        $block = $this->getLayout()->createBlock('extension/computer')
        ->setTemplate('catalog/product/newproductsajax.phtml');
         $this->getResponse()->setBody($block->toHtml());
    }

    public function ajaxbestsellerphoneAction(){
        $block = $this->getLayout()->createBlock('extension/phone')
        ->setTemplate('catalog/product/computeraccessoriesAjax.phtml');
         $this->getResponse()->setBody($block->toHtml());
    }

    public function ajaxnewproductphoneAction(){
        $block = $this->getLayout()->createBlock('extension/phone')
        ->setTemplate('catalog/product/newproductsajax.phtml');
         $this->getResponse()->setBody($block->toHtml());

    }

    public function ajaxlatestproductAction(){
        $block = $this->getLayout()->createBlock('extension/category')
        ->setTemplate('catalog/category/ajaxlatestproduct.phtml');
         $this->getResponse()->setBody($block->toHtml());
    }

    public function ajaxbestsellerproductAction(){
        $block = $this->getLayout()->createBlock('extension/category')
        ->setTemplate('catalog/category/ajaxbestseller.phtml');
         $this->getResponse()->setBody($block->toHtml());
    }
    
    public function ajaxupcomingAction(){
        $block = $this->getLayout()->createBlock('extension/category')
        ->setTemplate('catalog/category/ajaxupcoming.phtml');
         $this->getResponse()->setBody($block->toHtml());
    }
    
}





