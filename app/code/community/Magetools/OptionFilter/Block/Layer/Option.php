<?php 
/**
 *
 * @category Magetools
 * @package Magetools_OptionFilter
 * @copyright Copyright (c) 2014 Magetools Magetools.net
 * @author Magetools
 *
 */
class Magetools_OptionFilter_Block_Layer_Option
extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_filterModelName = 'magetools_optionfilter/layer_option';
    }
 
    protected function _prepareFilter()
    {
        $this->_filter->setOptionCode($this->getOptionCode());
        return $this;
    }
}
