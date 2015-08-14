<?php

class TM_SmartSuggest_Model_Config_Mode
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'grid',   'label' => Mage::helper('catalog')->__('Grid')),
            array('value' => 'list',   'label' => Mage::helper('catalog')->__('List')),
            array('value' => 'slider', 'label' => Mage::helper('smartsuggest')->__('Slider'))
        );
    }
}
