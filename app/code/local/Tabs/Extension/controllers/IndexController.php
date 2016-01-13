<?php
require_once(Mage::getModuleDir('controllers','Tabs_Extension').DS.'BaseController.php');
class Tabs_Extension_IndexController extends Tabs_Extension_BaseController
{

    public function indexAction() {

	 $this->_redirect('/');

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

     public function dealsAction()
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

     public function perfumeAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ajaxbestsellerhomeAction(){
       $block = $this->getLayout()->createBlock('extension/seller')
        ->setTemplate('catalog/product/bestsellerAjax.phtml');
         $this->getResponse()->setBody($block->toHtml());

    }

    public function ajaxdealsAction(){
       $block = $this->getLayout()->createBlock('extension/sale')
        ->setTemplate('catalog/product/ajaxdeals.phtml');
         $this->getResponse()->setBody($block->toHtml());

    }

    public function ajaxdealshomeAction(){
       $block = $this->getLayout()->createBlock('extension/sale')
        ->setTemplate('catalog/product/todays_dealsAjax.phtml');
         $this->getResponse()->setBody($block->toHtml());

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
        $this->setResponseForCurrentUriWithMemcache('extension/phone', 'newproductsajax.phtml');
    }
    public function ajaxbestsellerperfumeAction(){
        $block = $this->getLayout()->createBlock('extension/perfume')
        ->setTemplate('catalog/product/computeraccessoriesAjax.phtml');
         $this->getResponse()->setBody($block->toHtml());
    }

    public function ajaxnewproductperfumeAction(){
        $block = $this->getLayout()->createBlock('extension/perfume')
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





