<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */  
require_once 'AbstractController.php';
 
class Amasty_Xnotif_StockController extends Amasty_Xnotif_AbstractController
{
    public function preDispatch()
    {
        parent::preDispatch();

        $this->_title= $this->__('My Out of Stock Subscriptions');
        $this->_type= "stock";
    }
} 