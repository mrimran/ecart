<?php
class MW_Mcore_Block_Mcore extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();		
    }
    
     public function getMcore()     
     { 
        if (!$this->hasData('mcore')) {
            $this->setData('mcore', Mage::registry('mcore'));
        }
        return $this->getData('mcore');
        
    }
    
    
}