<?php

class TM_SuggestPage_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize product instance from request data
     *
     * @param int $productId
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct($productId)
    {
        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
        if ($product->getId()) {
            return $product;
        }
        return false;
    }

    public function indexAction()
    {
        $this->loadLayout()
            ->_initLayoutMessages('checkout/session')
            ->_initLayoutMessages('catalog/session');

        $session    = Mage::getSingleton('checkout/session');
        $productId  = $session->getSuggestpageProductId(); // see TM_SuggestPage_Model_Observer
        // $session->setSuggestpageProductId(false);
        if ($productId && $product = $this->_initProduct($productId)) {
            Mage::register('product', $product);
        }

        $this->renderLayout();
    }
}
