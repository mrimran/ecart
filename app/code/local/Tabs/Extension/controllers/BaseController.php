<?php

/**
 * User: imran
 * Date: 1/13/16
 * Time: 3:15 PM
 */
class Tabs_Extension_BaseController extends Mage_Core_Controller_Front_Action
{
    const CACHE_FOR_HOUR = 3600;
    const CACHE_FOR_HALF_HOUR = 1800;
    const USE_CACHE = true;//set it to true to enable memcache on controllers
    public $dataHelper = null;
    public $canConnectToMemcache = false;
    public $memcacheCompress = 0;//TO Enable use MEMCACHE_COMPRESSED

    protected function _init()
    {
        $this->dataHelper = Mage::helper('extension');//get default data helper
        if (self::USE_CACHE) {
            $this->canConnectToMemcache = $this->dataHelper->memcacheConnect();
        }
    }

    public function setResponseForCurrentUriWithMemcache(
        $block,
        $template,
        $path = "catalog/product/",
        $memcacheSeconds = self::CACHE_FOR_HOUR
    ) {
        $this->_init();
        $key = print_r($this->getRequest()->getParams(), true) . Mage::app()->getRequest()->getRequestUri();
        $memcacheKey = $this->dataHelper->generateMemcacheKey($key);
        $html = $this->dataHelper->memcacheGet($memcacheKey);
        if (!$html) {
            $this->printDebugInfo("New Data");
            $html = $this->getLayout()->createBlock($block)
                ->setTemplate($path . $template)->toHtml();
            $this->dataHelper->memcacheSet($memcacheKey, $html, $memcacheSeconds, $this->memcacheCompress);
        } else {
            $this->printDebugInfo("Memcached Data");
        }
        $this->getResponse()->setBody($html);
    }

    public function getBrandId()
    {
        return ($this->getRequest()->getParam('brand_ids')) ? $this->getRequest()->getParam('brand_ids') : 0;
    }

    public function printDebugInfo($message)
    {
        if(Mage::app()->getRequest()->getParam('debug')) {
            echo "debug:".$message;
        }
    }
}