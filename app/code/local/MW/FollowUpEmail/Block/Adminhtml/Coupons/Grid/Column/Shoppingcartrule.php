<?php

class MW_FollowUpEmail_Block_Adminhtml_Coupons_Grid_Column_Shoppingcartrule extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text

{

    public function render(Varien_Object $row)

    {        

		$shoppingcartruleid = $row->getData($this->getColumn()->getIndex());

		if($shoppingcartruleid > 0){

			$rule = Mage::getModel('salesrule/rule')->load($shoppingcartruleid); 

			return $rule->getName();

		}

        return "";        

    }

}

