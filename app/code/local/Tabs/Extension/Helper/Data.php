<?php
class Tabs_Extension_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $memcache = null;
    public $canConnectToMemcache = false;
    public $connectedToMemcache = false;
    public $memcacheCompress = 0;//to enable use MEMCACHE_COMPRESSED
    const CACHE_FOR_HOUR = 3600;
    const CACHE_FOR_HALF_HOUR = 1800;
    const USE_CACHE = true;//set it to true to enable memcache on block collections
    protected $urls = array();
    public function __construct()
    {
        $this->__initMemcache();
    }

    private function __initMemcache()
    {
        if(self::USE_CACHE) {
            $this->canConnectToMemcache = $this->memcacheConnect();
            $this->memcacheCompress = MEMCACHE_COMPRESSED;
        }
    }

    public function _fullfillsMemcachePrereq()
    {
        if($this->connectedToMemcache) {
            return true;
        }
        //try reconnecting
        $this->__initMemcache();
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

    public function getCustomUrl($key)
    {
        //get friendly urls
        $superdeals = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('superdeals/')->getData('request_path');
        $this->urls['superdeals'] = ($superdeals) ? $superdeals : "extension/index/deals/";
        $bestselling = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('bestselling/')->getData('request_path');
        $this->urls['bestselling'] = ($bestselling) ? $bestselling : "extension/index/seller/";
        $collection = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('collection/')->getData('request_path');
        $this->urls['collection'] = ($collection) ? $collection : "extension/index/ourcollection/";
        $sale = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('sale/')->getData('request_path');
        $this->urls['sale'] = ($sale) ? $sale : "extension/index/sale/";
        $todaysdeals = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('todaysdeals/')->getData('request_path');
        $this->urls['todaysdeals'] = ($todaysdeals) ? $todaysdeals : "extension/category/sale/";
        $latestproduct = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('latestproduct/')->getData('request_path');
        $this->urls['latestproduct'] = ($latestproduct) ? $latestproduct : "extension/category/latest/";
        $bestseller = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('bestseller/')->getData('request_path');
        $this->urls['bestseller'] = ($bestseller) ? $bestseller : "extension/category/seller/";
        $upcoming = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getStoreId())->loadByRequestPath('upcoming/')->getData('request_path');
        $this->urls['upcoming'] = ($upcoming) ? $upcoming : "extension/category/upcoming/";

        return $this->urls[$key];
    }

    public function getCollectionCountWithMemcache($key, $obj, $methods = [])
    {
        $memcacheKey = $key;
        $count = $this->memcacheGet($memcacheKey);
        if(!$count) {
            foreach($methods as $method) {
                $collection = $obj->$method();
                $count = $collection->count();
                if($count) {
                    break;//don't go further as we've found the count.
                }
            }
            $this->memcacheSet($memcacheKey, $count, 86400, 0);//cache for 24 hours
        }
        return $count;
    }
}
	 