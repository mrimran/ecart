<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */   
class Amasty_Xnotif_Helper_Data extends Mage_Core_Helper_Url
{
    
    public function getStockAlert($product, $isLogged, $simple = 0)
    {
        if (!$product->getId() || !Mage::registry('current_product')) // this is the extension's setting.
        {
            return '';
        }
        $tempCurrentProduct = Mage::registry('current_product');
        
        Mage::unregister('par_product_id');
        Mage::unregister('product');
        Mage::unregister('current_product');

        Mage::register(
            'par_product_id',
            Mage::registry('main_product')
                ? Mage::registry('main_product')->getId()
                : $tempCurrentProduct->getId()
        );
        Mage::register('current_product', $product);
        Mage::register('product', $product);
        $alertBlock = Mage::app()->getLayout()->createBlock('productalert/product_view', 'productalert.stock.'.$product->getId());
        if ($alertBlock && !$product->getData('amxnotif_hide_alert') )
        {
            if( !$isLogged && !Mage::getStoreConfig('amxnotif/stock/disable_guest')){
                if($simple){
                      $alertBlock->setTemplate('amasty/amxnotif/product/view_email_simple.phtml');
                }else{
                      $alertBlock->setTemplate('amasty/amxnotif/product/view_email.phtml');
                }
            }
            else{
                $alertBlock->setTemplate('amasty/amxnotif/product/view.phtml');
                $alertBlock->prepareStockAlertData();
                $alertBlock->setHtmlClass('alert-stock link-stock-alert');
                $alertBlock->setSignupLabel($this->__('Sign up to get notified when this configuration is back in stock'));
            }

            $html = $alertBlock->toHtml();
            Mage::unregister('product');
            Mage::unregister('current_product');
            Mage::register('current_product', $tempCurrentProduct);
            Mage::register('product', $tempCurrentProduct);

            return $html;
        }
        
        Mage::unregister('product');
        Mage::unregister('current_product');
        Mage::register('current_product', $tempCurrentProduct);
        Mage::register('product', $tempCurrentProduct);
            
        return '';
    }
    
    public function getPriceAlert($product, $isLogged)
    {
        if (!$product->getId() || !Mage::registry('current_product')) // this is the extension's setting.
        {
            return '';
        }
        $tempCurrentProduct = Mage::registry('current_product');
        
        Mage::unregister('par_product_id');
        Mage::unregister('product');
        Mage::unregister('current_product');
        
        Mage::register('par_product_id', $tempCurrentProduct->getId());
        Mage::register('current_product', $product);
        Mage::register('product', $product);
        $alertBlock = Mage::app()->getLayout()->createBlock('productalert/product_view', 'productalert.price'.$product->getId());
        if ($alertBlock)
        {
            if( !$isLogged && !Mage::getStoreConfig('amxnotif/price/disable_guest')){
                $alertBlock->setTemplate('amasty/amxnotif/product/price/view_email_simple.phtml');
                $html = $alertBlock->toHtml();

                Mage::unregister('product');
                Mage::unregister('current_product');
                Mage::register('current_product', $tempCurrentProduct);
                Mage::register('product', $tempCurrentProduct);

                return $html;
            }
        }
        
        Mage::unregister('product');
        Mage::unregister('current_product');
        Mage::register('current_product', $tempCurrentProduct);
        Mage::register('product', $tempCurrentProduct);
            
        return '';
    }
    
    public function getProduct()
    {
        return Mage::registry('product');
    }
    
    public function getSignupUrl($type){
         return $this->_getUrl('xnotif/email/' . $type, array(
                    'product_id'    => $this->getProduct()->getId(),  
                    'parent_id'     => Mage::registry('par_product_id'),
                     Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
                ));
    }

    public function getEmailUrl($type){
      
         return $this->_getUrl('xnotif/email/' . $type);
    }
}