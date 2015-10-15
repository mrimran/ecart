<?php
 
class MyExtensions_Observerdemo_Model_Observer {
 
    public function addtocartEvent(Varien_Event_Observer $observer) {
$sku = $observer->getEvent()->getQuoteItem()->getProduct()->getData('sku');
        $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

        $new_price = $_product->getPrice();
        //$productId = $observer->getEvent()->getQuoteItem()->getProductId();

        /* $item = $observer->getQuoteItem();
          $item->setCustomPrice($new_price);
          $item->setOriginalCustomPrice($new_price);
          $item->setFinalPrice($new_price);
          $item->getProduct()->setIsSuperMode(true); */

        // Get the quote item
        $item = $observer->getQuoteItem();
        // Ensure we have the parent item, if it has one
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        // Load the custom price
        $price = $new_price;
        // Set the custom price
        
        //Set price by subtracting base price from the passed price and 
        //then adding the product price to tackle any extra price attached with custom options :)
        //identify if there is some extra cost, final price - $new_price or price
        $extra_price = $item->getProduct()->getFinalPrice() - $price;//add this extra price
        $extra_price = ($extra_price > 0) ? $extra_price : 0;
        echo "final price:".$item->getProduct()->getFinalPrice().", price:".$item->getProduct()->getPrice().", Passed:".($extra_price + $price); die();
        $item->setCustomPrice($extra_price + $price);
        $item->setOriginalCustomPrice($extra_price + $price);
        // Enable super mode on the product.
        $item->getProduct()->setIsSuperMode(true);
    }
}