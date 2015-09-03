<?php
/**
 *
 * @category Magetools
 * @package Magetools_OptionFilter
 * @copyright Copyright (c) 2014 Magetools Magetools.net
 * @author Magetools
 *
 */
class Magetools_OptionFilter_Helper_Data extends Mage_Core_Helper_Abstract
{
  /** 
   *  Get option code
   *
   *  @param int $option_id
   *  @return string 
   */
  public function getOptionCode($option_id){
    $option = Mage::getModel('catalog/product_option')->load($option_id);
    $option_code = '';
    if($option->getId()){
      $option_code = $option->getOptionCode();
    }
    return $option_code;
  }
}