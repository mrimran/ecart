<?php
/**
 *
 * @category Magetools
 * @package Magetools_OptionFilter
 * @copyright Copyright (c) 2014 Magetools Magetools.net
 * @author Magetools
 *
 */
class Magetools_OptionFilter_Block_Layer_View extends Mage_Catalog_Block_Layer_View
{
  /**
   * get all products custom select options
   *
   * @return Mage_Catalog_Product_Option
   */
  protected function _getCustomOptions(){
    if(!Mage::getStoreConfig('magetools_optionfilter/setting/enabled')){
      return array();
    }
    if($this->getCustomOptions()){
      return $this->getCustomOptions();
    }
    $options = Mage::getModel('catalog/product_option')->getCollection()
      ->addFieldToFilter('type',array('in'=>array(
        'drop_down',
        'radio',
        'checkbox',
        'multiple'
      )))
    ;
    $options
      ->getSelect()->group('option_code');
    $this->setCustomOptions($options);
    return $options;
  }
  
  /**
   * Prepare child blocks
   *
   * @return Mage_Catalog_Block_Layer_View
   */
  protected function _prepareLayout()
  {
    $options = $this->_getCustomOptions();
    foreach ($options as $option){
      $block = $this->getLayout()->createBlock('magetools_optionfilter/layer_option')
        ->setOptionCode($option->getOptionCode())->setLayer($this->getLayer())->init();
       $this->setChild('option_'.$option->getOptionCode().'_filter', $block);
    }
    return parent::_prepareLayout();
  }

  /**
   * Get all layer filters
   *
   * @return array
   */
  public function getFilters()
  {
    $options = $this->_getCustomOptions();
    $filters = parent::getFilters();
    $params = Mage::app()->getRequest()->getParams();
    foreach ($options as $option){
      $option_code = $option->getOptionCode();
      $option_filter = $this->getChild('option_'.$option_code.'_filter');
      if ( isset($option_filter) && !array_key_exists($option_code ,$params) ){
        $filters[] = $option_filter;
      }
    }
    return $filters;
  }
    
}