<?php

class MW_Mcore_Model_Mcore extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mcore/mcore');
    }
}