<?php

class MW_FollowUpEmail_Block_Adminhtml_Coupons_Grid_Column_Emptydate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime

{

    public function render(Varien_Object $row)

    {		

        if($row->getData($this->getColumn()->getIndex())){
			if($row->getData($this->getColumn()->getIndex()) == '0000-00-00 00:00:00')
			return 'Not sent yet';
			else
			return parent::render($row);		
		}

            

        else return $this->getColumn()->getEmptyText();

    }

}

