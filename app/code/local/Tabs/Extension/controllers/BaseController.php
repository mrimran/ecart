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

    public function __construct(
        Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        array $invokeArgs
    ) {
        parent::__construct($request, $response, $invokeArgs);
        $this->canConnectToMemcache = $this->memcacheConnect();
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
        return $this->memcache->set($key, $data, $compress, $seconds);
    }

    public function memcacheGet($key)
    {
        return $this->memcache->get($key);
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