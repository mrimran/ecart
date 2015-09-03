<?php
/**
 *
 * @category Magetools
 * @package Magetools_OptionFilter
 * @copyright Copyright (c) 2014 Magetools Magetools.net
 * @author Magetools
 *
 */
class Magetools_OptionFilter_Model_Observer {
  
  /**
   * override the template of option edit tab
   *
   * @param   Varien_Event_Observer $observer
   */
  public function layerLoadBefore(Varien_Event_Observer $observer) {
    $block = $observer->getBlock();
    if (Mage::getStoreConfig('magetools_optionfilter/setting/enabled')
      && $block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option
    ) {
      $block->setTemplate('magetools/optionfilter/product_option.phtml');
    }
  }

}