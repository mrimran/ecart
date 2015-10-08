<?php
class Tabs_Extension_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $urls = array();
    public function getCustomUrl($key)
    {
        //get friendly urls
        $superdeals = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('superdeals/')->getData('request_path');
        $this->urls['superdeals'] = ($superdeals) ? $superdeals : "extension/index/deals/";
        $this->bestselling = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('bestselling/')->getData('request_path');
        $this->urls['bestselling'] = ($bestselling) ? $bestselling : "extension/index/seller/";
        $collection = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('collection/')->getData('request_path');
        $this->urls['collection'] = ($collection) ? $collection : "extension/index/ourcollection/";
        $sale = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('sale/')->getData('request_path');
        $this->urls['sale'] = ($sale) ? $sale : "extension/index/sale/";
        
        return $this->urls[$key];
    }
}
	 