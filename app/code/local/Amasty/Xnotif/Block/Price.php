<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */       
class Amasty_Xnotif_Block_Price extends Amasty_Xnotif_Block_Abstract
{
    public function __construct()
    {
        $this->_title= $this->__('My Price Subscriptions');
        $this->_type= "price";
        
        parent::__construct();
    }
}
 