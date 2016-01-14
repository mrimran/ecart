<?php

/**
 * User: imran
 * Date: 1/13/16
 * Time: 3:15 PM
 */
class Tabs_Extension_Block_Base extends Mage_Catalog_Block_Product_Abstract
{
    const CACHE_FOR_HOUR = 3600;
    const CACHE_FOR_HALF_HOUR = 1800;
    public $memcacheCompress = 0;//TO Enable use MEMCACHE_COMPRESSED
}