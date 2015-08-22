<?php
// app/code/local/Envato/Recentproducts/Block/Recentproducts.php
class Tabs_Extension_Block_Related extends Mage_Catalog_Block_Product_Abstract {
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
    protected $_productsCount = null;
    const DEFAULT_PRODUCTS_COUNT = 10;

    public function getLoadedRelProduct()
    { 

       $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        $max = 0;
        $lastItem = null;         
        $relatedCollection = new Varien_Data_Collection();
        foreach ($items as $item){
            if ($item->getId() > $max) {
                $max = $item->getId();
                $lastItem = $item;
                

            }

            
        }  
        $related_prods = $item->getProduct()->getRelatedProductIds(); 
        return $related_prods;
    } 

   
   
   

}
