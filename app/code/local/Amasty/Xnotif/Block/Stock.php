<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */       
class Amasty_Xnotif_Block_Stock extends Amasty_Xnotif_Block_Abstract
{
    public function __construct()
    {
        $this->_title= $this->__('My Stock Subscriptions');
        $this->_type= "stock";
        
        parent::__construct();
    }
}
 