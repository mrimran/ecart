<?php

/**
 * User: imran
 * Date: 1/13/16
 * Time: 3:15 PM
 */
class Tabs_Extension_Block_Base extends Mage_Catalog_Block_Product_Abstract
{
    public $memcache = null;
    public $canConnectToMemcache = false;
    public $connectedToMemcache = false;
    public $memcacheCompress = 0;//to enable use MEMCACHE_COMPRESSED
    const CACHE_FOR_HOUR = 3600;
    const CACHE_FOR_HALF_HOUR = 1800;
    const USE_CACHE = true;//set it to true to enable memcache on block collections
    public function __construct(array $args)
    {
        parent::__construct($args);
        if(self::USE_CACHE) {
            $this->canConnectToMemcache = $this->memcacheConnect();
            $this->memcacheCompress = MEMCACHE_COMPRESSED;
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
                $this->memcache->pconnect('localhost', 11211);
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
}