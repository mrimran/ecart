<?php
class MW_FollowUpEmail_Block_Adminhtml_Queue_Grid_Column_Emptydate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
{
    public function render(Varien_Object $row)
    {		
        if($row->getData($this->getColumn()->getIndex()))
            return parent::render($row);
        else return $this->getColumn()->getEmptyText();
    }
}
