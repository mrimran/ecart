<?php
class MW_FollowUpEmail_Block_Followupemail extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getFollowupemail()     
     { 
        if (!$this->hasData('followupemail')) {
            $this->setData('followupemail', Mage::registry('followupemail'));
        }
        return $this->getData('followupemail');
        
    }
}