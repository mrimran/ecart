<?php
/**
 * 
 *
 */



class Techninja_Share_Model_System_Config_Source_Side extends Mage_Core_Block_Template
{
    public function toOptionArray()
    {
       return array(
            array(
                'value' => 'left',
                'label' => 'Left',
            ),
            array(
                'value' => 'right',
                'label' => 'Right',
            ),
        );
    }
	
	
}