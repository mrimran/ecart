<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */  
class Amasty_Xnotif_Model_Observer extends Mage_ProductAlert_Model_Observer
{
    protected function _processStock(Mage_ProductAlert_Model_Email $email)
    {
        $this->_foreachAlert('stock', $email);
    }
    
    protected function _processPrice(Mage_ProductAlert_Model_Email $email)
    {
        $this->_foreachAlert('price',  $email);
    }
    
    public function handleBlockAlert($observer) 
    {
        /* @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();
        
        $transport = $observer->getTransport();
        $html = $transport->getHtml();
        $pos = strpos($html, 'alert-stock');
        if ($block instanceof Mage_Productalert_Block_Product_View && $pos && !Mage::getStoreConfig('amxnotif/stock/disable_guest')) {
            $isLogged = Mage::helper('customer')->isLoggedIn();
            if(!$isLogged) {
                preg_match('#product_id/([0-9]+)/#', $html, $result);
                if($result) { 
                    $result = array();
                    $product = Mage::registry('current_product');
                    if (!$product->isSaleable()){
                        $blockHtml = Mage::helper('amxnotif')->getStockAlert($product, $isLogged, 1);
                        $html = $blockHtml;
                        $transport->setHtml($html);
                    }
                }
            }
                
        }
        
        $pos = strpos($html, 'alert-price');
        if ($block instanceof Mage_Productalert_Block_Product_View && $pos && !Mage::getStoreConfig('amxnotif/price/disable_guest')) {
            preg_match('#product_id/([0-9]+)/#', $html, $result);
            if($result && !Mage::helper('customer')->isLoggedIn()) {
                $result = array();
                $product = Mage::registry('current_product');
                $blockHtml = Mage::helper('amxnotif')->getPriceAlert($product,  Mage::helper('customer')->isLoggedIn());
                $html = $blockHtml;
                $transport->setHtml($html);
            }
                
        }
    }
    
    public function runProductalertObserver()
    {/*
        if(Mage::getStoreConfig('amxnotif/general/send_observer'))  
        {
            try{
                $object = new Varien_Object();
                $observer = Mage::getSingleton('productalert/observer');
                $observer->process($object);
            }
            catch(Exception $exc){}
        }*/
    }

    protected function _foreachAlert($type, $email)
    {

        $email->setType($type);
        foreach ($this->_getWebsites() as $website) {
            /* @var $website Mage_Core_Model_Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }
            
            if (!Mage::getStoreConfig(self::XML_PATH_STOCK_ALLOW, $website->getDefaultGroup()->getDefaultStore()->getId())) {
                continue;
            }
            
            try {
                $collection = Mage::getModel('productalert/' . $type)
                    ->getCollection()
                    ->addWebsiteFilter($website->getId())
                    ->addFieldToFilter('status', 0)
                    ->setCustomerOrder();
            }
            catch (Exception $e) {
                Mage::log($e->getMessage());
                $this->_errors[] = $e->getMessage();
                return $this;
            }
            $previousCustomer = null;
            $email->setWebsite($website);
            
            foreach ($collection as $alert) {
                try {
                    $isGuest = (0 == $alert->getCustomerId())? 1: 0;

                    if (!$previousCustomer || ($previousCustomer->getId() != $alert->getCustomerId()) || ($previousCustomer->getEmail() != $alert->getEmail())) {
                        if($isGuest){
                            $customer = Mage::getModel('customer/customer') ;
                            $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                            $customer->loadByEmail($alert->getEmail());
                            
                            if(!$customer->getId()){ 
                                $customer->setEmail($alert->getEmail());
                                $customer->setFirstname(Mage::getStoreConfig('amxnotif/general/customer_name'));
                                $customer->setGroupId(0);
                                $customer->setId(0);
                            }
                        }
                        else{
                            $customer = Mage::getModel('customer/customer')->load($alert->getCustomerId());
                        }
                        if ($previousCustomer) {
                            $email->send();
                            $this->unsubscribe($alert->getProductId(), $email->getCustomer(), $isGuest, $website, $type);
                        }

                        if (!$customer) {
                            continue;
                        }
                        $previousCustomer = $customer;
                        $email->clean();
                        $email->setCustomer($customer);
                    }
                    else {
                        $customer = $previousCustomer;
                    }

                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($website->getDefaultStore()->getId())
                        ->load($alert->getProductId());
                    /* @var $product Mage_catalog_Model_Product */
                    if (!$product) {
                        continue;
                    }                   
                      
                    $product->setCustomerGroupId($customer->getGroupId());

                    /*
                     * check alert data by type
                     * */
                    if('stock' == $type){
                        $minQuantity = Mage::getStoreConfig('amxnotif/general/min_qty');
                        if($minQuantity < 1) $minQuantity = 1;

                        $isInStock = false;
                        if ($product->isConfigurable() && $product->isInStock()) {
                            $allProducts = $product->getTypeInstance(true)
                                    ->getUsedProducts(null, $product);

                            foreach ($allProducts as $simpleProduct) {
                                $stockItem   = Mage::getModel('cataloginventory/stock_item')->loadByProduct($simpleProduct);
                                $quantity = $stockItem->getData('qty');
                                $isInStock = ($simpleProduct->isSalable() || $simpleProduct->isSaleable())
                                    && $quantity >= $minQuantity;
                                if ($isInStock) {
                                    break;
                                }
                            }
                        } else {
                            $stockItem   = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                            $quantity = $stockItem->getData('qty');
                            $isInStock = ($product->isSalable() || $product->isSaleable())
                                && $quantity >= $minQuantity;
                        }
                        if ($isInStock) {
                            if($alert->getParentId() && !$product->isConfigurable()){
                                $product = Mage::getModel('catalog/product')
                                    ->setStoreId($website->getDefaultStore()->getId())
                                    ->load($alert->getParentId());
                            }

                            $email->addStockProduct($product);
                            $alert->setSendDate(Mage::getModel('core/date')->gmtDate());

                            $alert->setSendCount($alert->getSendCount() + 1);
                            $alert->setStatus(1);
                            $alert->save();
                        }
                    }
                    else{
                        if ($alert->getPrice() > $product->getFinalPrice()) {
                            $productPrice = $product->getFinalPrice();
                            $product->setFinalPrice(Mage::helper('tax')->getPrice($product, $productPrice));
                            $product->setPrice(Mage::helper('tax')->getPrice($product, $product->getPrice()));
                            $email->addPriceProduct($product);

                            $alert->setPrice($productPrice);
                            $alert->setLastSendDate(Mage::getModel('core/date')->gmtDate());

                            $alert->setSendCount($alert->getSendCount() + 1);
                            $alert->setStatus(1);
                            $alert->save();
                        }
                    }

                }
                catch (Exception $e) {
                    Mage::log($e->getMessage());
                    $this->_errors[] = $e->getMessage();
                }
            }
            if ($previousCustomer) {
                try {
                    $email->send();
                    $this->unsubscribe($alert->getProductId(), $email->getCustomer(), $isGuest, $website, $type);
                }
                catch (Exception $e) {
                    Mage::log($e->getMessage());
                    $this->_errors[] = $e->getMessage();
                }
            }
        }
        return $this;    
    }
    
    private function unsubscribe($productId, $customer, $isGuest, $website, $type) 
    {
	    try {
            if (!$productId || (!$isGuest && !Mage::getStoreConfig('amxnotif/' . $type . '/unsubscribeC')) || ($isGuest && !Mage::getStoreConfig('amxnotif/' . $type . '/unsubscribeG'))) {
                return;
            }
            $product = Mage::getModel('catalog/product')->load($productId);
            if (!$product->getId() || !$product->isVisibleInCatalog()) {
                Mage::log('The product was not found.');
                return ;
            }
            $_customerId = (!$isGuest && $customer && $customer->getId())? $customer->getId() : 0;

            $model  = Mage::getModel('productalert/' . $type)
                ->setCustomerId($_customerId)
                ->setProductId($product->getId())
                ->setWebsiteId($website->getId())
                ->loadByParam();
            if ($model->getId()) {
                $model->delete();

                return true;
            }
        }
        catch (Exception $e) {
             Mage::log($e->getMessage());
             Mage::log('Unable to update the alert subscription.');
             
		     return false;
        }
        
	    return false;
    }
}