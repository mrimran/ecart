<?php

class YBizz_PriceChange_Model_Observer  {

public function change_price(Varient_Event_Observer $observer) {

        $sku=$observer->getEvent()->getQuoteItem()->getProduct()->getData('sku'); 
		$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);

        $new_price = $_product->getPrice();
		//$productId = $observer->getEvent()->getQuoteItem()->getProductId();

        /*$item = $observer->getQuoteItem();
        $item->setCustomPrice($new_price);
        $item->setOriginalCustomPrice($new_price);
		$item->setFinalPrice($new_price);
        $item->getProduct()->setIsSuperMode(true);*/
		
		 // Get the quote item
        $item = $observer->getQuoteItem();
        // Ensure we have the parent item, if it has one
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        // Load the custom price
        $price = $new_price;
        // Set the custom price
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        // Enable super mode on the product.
        $item->getProduct()->setIsSuperMode(true);
        
    }

}