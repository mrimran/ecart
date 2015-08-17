<?php
/**
 * 
 *
 */



class Techninja_Share_Model_System_Config_Source_Size extends Mage_Core_Block_Template
{
    public function toOptionArray()
    {
       return array(
            array(
                'value' => 'large',
                'label' => 'Large',
            ),
            array(
                'value' => 'small',
                'label' => 'Small',
            ),
        );
    }
	
	
}