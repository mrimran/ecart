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
        $this->setResponseForCurrentUriWithMemcache('extension/seller', 'bestsellerAjax.phtml');
    }

    public function ajaxdealsAction(){
        $this->setResponseForCurrentUriWithMemcache('extension/sale', 'ajaxdeals.phtml');
    }

    public function ajaxdealshomeAction(){
        $this->setResponseForCurrentUriWithMemcache('extension/sale', 'todays_dealsAjax.phtml');

    }
    public function ajaxbestsellerAction(){
        $this->setResponseForCurrentUriWithMemcache('extension/computer', 'computeraccessoriesAjax.phtml');

    }

     public function ajaxnewproductAction(){
         $this->setResponseForCurrentUriWithMemcache('extension/computer', 'newproductsajax.phtml');
    }

    public function ajaxbestsellerphoneAction(){
        $this->setResponseForCurrentUriWithMemcache('extension/phone', 'computeraccessoriesAjax.phtml');
    }

    public function ajaxnewproductphoneAction(){
        $this->setResponseForCurrentUriWithMemcache('extension/phone', 'newproductsajax.phtml');
    }
    public function ajaxbestsellerperfumeAction(){
        $this->setResponseForCurrentUriWithMemcache('extension/perfume', 'computeraccessoriesAjax.phtml');
    }

    public function ajaxnewproductperfumeAction(){
        $this->setResponseForCurrentUriWithMemcache('extension/perfume', 'newproductsajax.phtml');
    }

    public function ajaxlatestproductAction(){
        $this->setResponseForCurrentUriWithMemcache('extension/category', 'ajaxlatestproduct.phtml');
    }

    public function ajaxbestsellerproductAction(){
        $this->setResponseForCurrentUriWithMemcache('extension/category', 'ajaxbestseller.phtml');
    }

    public function ajaxupcomingAction(){
        $this->setResponseForCurrentUriWithMemcache('extension/category', 'ajaxupcoming.phtml');
    }

}





