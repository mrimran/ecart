<?php
require_once(Mage::getModuleDir('controllers', 'Tabs_Extension') . DS . 'BaseController.php');

class Tabs_Extension_IndexController extends Tabs_Extension_BaseController
{

    public function indexAction()
    {

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

    public function phoneAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function computerAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function perfumeAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ajaxbestsellerhomeAction()
    {
        $this->getHtmlForCurrentUriWithMemcache('extension/seller', 'bestsellerAjax.phtml');
    }

    public function ajaxdealsAction()
    {
        $block = $this->getLayout()->createBlock('extension/sale')
            ->setTemplate('catalog/product/ajaxdeals.phtml');
        $this->getResponse()->setBody($block->toHtml());
        $this->getHtmlForCurrentUriWithMemcache('extension/sale', 'ajaxdeals.phtml');

    }

    public function ajaxdealshomeAction()
    {
        $html = $this->getHtmlForCurrentUriWithMemcache('extension/sale', 'todays_dealsAjax.phtml');
        $this->getResponse()->setBody($html);
    }

    public function ajaxbestsellerAction()
    {
        $html = $this->getHtmlForCurrentUriWithMemcache('extension/computer', 'computeraccessoriesAjax.phtml');
        $this->getResponse()->setBody($html);
    }

    public function ajaxnewproductAction()
    {
        $html = $this->getHtmlForCurrentUriWithMemcache('extension/computer', 'newproductsajax.phtml');
        $this->getResponse()->setBody($html);
    }

    public function ajaxbestsellerphoneAction()
    {
        $html = $this->getHtmlForCurrentUriWithMemcache('extension/phone', 'computeraccessoriesAjax.phtml');
        $this->getResponse()->setBody($html);
    }

    public function ajaxnewproductphoneAction()
    {
        $html = $this->getHtmlForCurrentUriWithMemcache('extension/phone', 'newproductsajax.phtml');
        $this->getResponse()->setBody($html);
    }

    public function ajaxbestsellerperfumeAction()
    {
        $html = $this->getHtmlForCurrentUriWithMemcache('extension/perfume', 'computeraccessoriesAjax.phtml');
        $this->getResponse()->setBody($html);
    }

    public function ajaxnewproductperfumeAction()
    {
        $html = $this->getHtmlForCurrentUriWithMemcache('extension/perfume', 'newproductsajax.phtml');
        $this->getResponse()->setBody($html);
    }

    public function ajaxlatestproductAction()
    {
        $html = $this->getHtmlForCurrentUriWithMemcache('extension/category', 'ajaxlatestproduct.phtml');
        $this->getResponse()->setBody($html);
    }

    public function ajaxbestsellerproductAction()
    {
        $html = $this->getHtmlForCurrentUriWithMemcache('extension/category', 'ajaxbestseller.phtml');
        $this->getResponse()->setBody($html);
    }

    public function ajaxupcomingAction()
    {
        $html = $this->getHtmlForCurrentUriWithMemcache('extension/category', 'ajaxupcoming.phtml');
        $this->getResponse()->setBody($html);
    }

}





