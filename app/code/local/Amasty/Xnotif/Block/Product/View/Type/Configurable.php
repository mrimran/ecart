<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
class Amasty_Xnotif_Block_Product_View_Type_Configurable extends Amasty_Xnotif_Block_Product_View_Type_Configurable_Pure
{
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        if ('product.info.options.configurable' == $this->getNameInLayout() && 'true' != (string)Mage::getConfig()->getNode('modules/Amasty_Stockstatus/active'))
        {
            $allProducts = $this->getProduct()->getTypeInstance(true)
                ->getUsedProducts(null, $this->getProduct());

            $_attributes = $this->getProduct()->getTypeInstance(true)->getConfigurableAttributes($this->getProduct());
            foreach ($allProducts as $product)
            {

                $key = array();
                foreach ($_attributes as $attribute)
                {
                    $key[] = $product->getData($attribute->getData('product_attribute')->getData('attribute_code'));
                }
                $stockStatus = '';
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                if (!$product->isInStock())
                {
                    $stockStatus = Mage::helper('amxnotif')->__('Out of Stock') ;
                } 
                if ($key)
                {
                    $aStockStatus[implode(',', $key)] = array(
                        'is_in_stock'   => $product->isSaleable(),
                        'custom_status' => $stockStatus,
                        'is_qnt_0'      => (int)($product->isInStock() && $stockItem->getData('qty') <= 0),
                        'product_id'    => $product->getId(),
                        'stockalert'	=> Mage::helper('amxnotif')->getStockAlert($product, $this->helper('customer')->isLoggedIn()),
                    );
                }
            }
            foreach ($aStockStatus as $k=>$v){
                if (!$v['is_in_stock'] && !$v['custom_status']){
                    $v['custom_status'] = Mage::helper('amxnotif')->__('Out of Stock');
                    $aStockStatus[$k] = $v;
                }   
            }
            $html = '<script type="text/javascript">
                        var changeConfigurableStatus = true;
                        var stStatus = new StockStatus(' . Zend_Json::encode($aStockStatus) . ');
                    </script>' . $html;
        }
        return $html;
    }
    
    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = array();
            $allProducts = $this->getProduct()->getTypeInstance(true)
                ->getUsedProducts(null, $this->getProduct());
            foreach ($allProducts as $product) {
                /**
                * Should show all products (if setting set to Yes), but not allow "out of stock" to be added to cart
                */
                    if ($product->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_DISABLED)
                    {
                        $products[] = $product;
                    }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }
}