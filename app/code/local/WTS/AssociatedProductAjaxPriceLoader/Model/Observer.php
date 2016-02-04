<?php

class WTS_AssociatedProductAjaxPriceLoader_Model_Observer
{

    public function changePrice(Varien_Event_Observer $observer)
    {
        
        
        $sku = $observer->getEvent()->getQuoteItem()->getProduct()->getData('sku');
        $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        $new_price = $_product->getPrice();
        $new_final_price = $_product->getFinalPrice();
        $hasSpecialPrice = $this->productHasSpecialPrice($_product);
        if($hasSpecialPrice) {
            //don't do anything in case of special price
            return;
        }
        // Get the quote item
        $item = $observer->getQuoteItem();
        $hasParent = false;
        $parentItem = null;
        if($item->getParentItem()) {
            $hasParent = true;
            $parentItem = $item->getParentItem();
        }
        //echo $new_price."|".$new_final_price;
        // Ensure we have the parent item, if it has one
        $item = ( $parentItem ? $parentItem : $item );
        //echo "SSS:".$specialprice."|".$specialPriceFromDate."|".$specialPriceToDate."<br>";
        $extra_price = $item->getProduct()->getFinalPrice() - $item->getProduct()->getPrice(); //add this extra price
        //echo ">>>".$extra_price.">>>".$item->getProduct()->getPrice().">>>".$item->getProduct()->getFinalPrice();die();
        //apply the payment logic on in configurable products only
        if($item->getProduct()->isConfigurable()) {
            // Load the custom price
            $price = $new_price;

            // Set the custom price
            //Set price by subtracting base price from the passed price and 
            //then adding the product price to tackle any extra price attached with custom options :)
            //identify if there is some extra cost, final price - base price
            $extra_price = $item->getProduct()->getFinalPrice() - $item->getProduct()->getPrice(); //add this extra price
            echo $item->getProduct()->getProductThumbnail();
           
            $extra_price = ($extra_price > 0) ? $extra_price : 0;
            $price = $price + $extra_price;
            
            /* echo "Got:". $price;
              echo " Extra: ".$extra_price;
              echo "final price:".$item->getProduct()->getFinalPrice().", price:".$item->getProduct()->getPrice().", Passed:".($extra_price + $price);die(); */
            //only apply the associated price if extra_price don't exist (means values are not being set with attributes)
            /*if($extra_price == 0) {
                $item->setCustomPrice($extra_price + $price);
                $item->setOriginalCustomPrice($extra_price + $price);
            }else {*/
                //if the attributes are defined, use the parent product price instead (base product price)
                $item->setCustomPrice($new_final_price);
                $item->setOriginalCustomPrice($new_final_price);
            //}
            // Enable super mode on the product.
            $item->getProduct()->setIsSuperMode(true);
        }
    }

    public function productHasSpecialPrice($_product)
    {
        $hasSpecialPrice = false;
        // Get the Special Price
        $specialprice = $_product->getSpecialPrice();
        // Get the Special Price FROM date
        $specialPriceFromDate = $_product->getSpecialFromDate();
        // Get the Special Price TO date
        $specialPriceToDate = $_product->getSpecialToDate();
        // Get Current date
        $today = time();

        if ($specialprice) {
            if ($today >= strtotime($specialPriceFromDate) && $today <= strtotime($specialPriceToDate) || $today >= strtotime($specialPriceFromDate) && is_null($specialPriceToDate)) {
                $hasSpecialPrice = true;
            }
        }
        return $hasSpecialPrice;
    }

}
