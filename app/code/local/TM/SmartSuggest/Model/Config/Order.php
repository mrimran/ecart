<?php

class TM_SmartSuggest_Model_Config_Order
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'popularity', 'label' => Mage::helper('reports')->__('Popularity')),
            array('value' => 'sales',      'label' => Mage::helper('sales')->__('Sales')),
            array('value' => 'random',     'label' => Mage::helper('smartsuggest')->__('Random'))
        );
    }
}
