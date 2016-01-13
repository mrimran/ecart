<?php

/**
 * User: imran
 * Date: 1/13/16
 * Time: 3:15 PM
 */
class Tabs_Extension_BaseController extends Mage_Core_Controller_Front_Action
{
    public $memcache = null;
    public $canConnectToMemcache = false;
    public $connectedToMemcache = false;
    public $memcacheCompress = 0;//TO Enable use MEMCACHE_COMPRESSED
    const CACHE_FOR_HOUR = 3600;
    const CACHE_FOR_HALF_HOUR = 1800;
    const USE_CACHE = true;//set it to true to enable memcache on controllers

    protected function _init()
    {
        if (self::USE_CACHE) {
            $this->canConnectToMemcache = $this->memcacheConnect();
        }
    }

    private function _fullfillsMemcachePrereq()
    {
        $this->_init();
        if ($this->connectedToMemcache) {
            return true;
        }
        return false;
    }

    public function memcacheConnect()
    {
        if (!$this->memcache) {
            if (class_exists('Memcache')) {
                $this->memcache = new Memcache;
                $this->memcache->pconnect('localhost', 11211);
                if ($this->memcache) {
                    $this->connectedToMemcache = true;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    public function memcacheSet($key, $data, $seconds = 1800, $compress = false)
    {
        if($this->_fullfillsMemcachePrereq()) {
            try {
                return $this->memcache->set($key, $data, $compress, $seconds);
            } catch (Exception $e) {
                //don't through error just return false which is at the bottom
            }
        }

        return false;
    }

    public function memcacheGet($key)
    {
        if ($this->_fullfillsMemcachePrereq()) {
            return $this->memcache->get($key);
        }
        return null;
    }

    public function generateMemcacheKey($keyData)
    {
        return crc32($keyData);
    }

    public function getBrandId()
    {
        return ($this->getRequest()->getParam('brand_ids')) ? $this->getRequest()->getParam('brand_ids') : 0;
    }

    public function setResponseForCurrentUriWithMemcache(
        $block,
        $template,
        $path = "catalog/product/",
        $memcacheSeconds = self::CACHE_FOR_HOUR
    ) {
        $key = print_r($this->getRequest()->getParams(), true) . Mage::app()->getRequest()->getRequestUri();
        $memcacheKey = $this->generateMemcacheKey($key);
        $html = $this->memcacheGet($memcacheKey);
        if (!$html) {
            $html = $this->getLayout()->createBlock($block)
                ->setTemplate($path . $template)->toHtml();
            $this->memcacheSet($memcacheKey, $html, $memcacheSeconds, $this->memcacheCompress);
        }
        $this->getResponse()->setBody($html);
    }
}