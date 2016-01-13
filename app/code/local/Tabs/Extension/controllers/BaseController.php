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
    public $memcacheCompress = true;
    const CACHE_FOR_HOUR = 3600;
    const CACHE_FOR_HALF_HOUR = 1800;
    const USE_CACHE = true;//set it to true to enable memcache on controllers
    public function __construct(array $args)
    {
        parent::__construct($args);
        if(self::USE_CACHE) {
            $this->canConnectToMemcache = $this->memcacheConnect();
        }
    }

    private function _fullfillsMemcachePrereq()
    {
        if($this->connectedToMemcache) {
            return true;
        }
        return false;
    }

    public function memcacheConnect()
    {
        if(!$this->memcache) {
            if(class_exists('Memcache')) {
                $this->memcache = new Memcache;
                $this->memcache->connect('localhost', 11211);
                if($this->memcache) {
                    $this->connectedToMemcache = true;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    public function memcacheSet($key, $data, $seconds=1800, $compress=false)
    {
        if($this->_fullfillsMemcachePrereq())
            return $this->memcache->set($key, $data, $compress, $seconds);

        return false;
    }

    public function memcacheGet($key)
    {
        if($this->_fullfillsMemcachePrereq())
            return $this->memcache->get($key);
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

    public function setResponseForCurrentUriWithMemcache($controller, $block, $template, $path = "catalog/product/", $memcacheSeconds = self::CACHE_FOR_HOUR)
    {
        $memcacheKey = $this->generateMemcacheKey(print_r($this->getRequest()->getParams(), true));
        $html = $this->memcacheGet($memcacheKey);
        if(!$html) {
            $html = $controller->getLayout()->createBlock($block)
                ->setTemplate($path.$template)->toHtml();
            $this->memcacheSet($memcacheKey, $html, $memcacheSeconds, $this->memcacheCompress);
        }
        $controller->getResponse()->setBody($html);
    }
}