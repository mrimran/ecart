<?php 
/**
 *
 * @category Magetools
 * @package Magetools_OptionFilter
 * @copyright Copyright (c) 2014 Magetools Magetools.net
 * @author Magetools
 *
 */
class Magetools_OptionFilter_Model_Layer_Option
extends Mage_Catalog_Model_Layer_Filter_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'attribute';
    }

    /**
     * Apply filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Magetools_OptionFilter_Model_Layer_Option
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->getRequestVar());
        if(!$filter){
          return $this;
        }
        $temp = explode('_v_', $filter);
        $option_value_id = array_pop($temp);
        $option_id = array_pop($temp);
        $options = Mage::getModel('catalog/product_option')->getCollection();
        $options
          ->addFieldToFilter('type',array('in'=>array(
            'drop_down',
            'radio',
            'checkbox',
            'multiple'
          )))
          ->addFieldToFilter('main_table.option_code',$this->getRequestVar())
          ->addTitleToResult(Mage::app()->getStore()->getId())
          ->addValuesToResult()
        ;
        $product_ids = array();
        $id_label = $id;
        foreach ($options as $option){          
            $option_values = Mage::getModel('catalog/product_option_value')
              ->getValuesCollection($option)
              ->toArray()
            ;
            foreach ($option_values['items'] as $ov) {
              $id = empty($ov['sku'])? $ov['title']:$ov['sku'];
              if($id == $option_value_id){
                $id_label = $ov['title'];
                $product_ids[] = $option->getProductId();
                break;
              }
            }

        }
        $products = $this->getLayer()->getProductCollection();
        $products->addAttributeToFilter('entity_id', array('in'=>$product_ids));
        $stateLabel = Mage::helper('magetools_optionfilter')->__(
          $id_label
        );
        
        $state = $this->_createItem(
            $stateLabel, $filter
        )->setVar($this->_requestVar);
 
        $this->getLayer()->getState()->addFilter($state);
 
        return $this;
    }
 
    /**
     * Get filter name
     *
     * @return string
     */
    public function getName()
    {
      $options = Mage::getModel('catalog/product_option')->getCollection();      
      $options
        ->addFieldToFilter('type',array('in'=>array(
          'drop_down',
          'radio',
          'checkbox',
          'multiple'
        )))
        ->addFieldToFilter('option_code',$this->getOptionCode())
        ->addTitleToResult(Mage::app()->getStore()->getId())
      ;
      return Mage::helper('magetools_optionfilter')->__($options->getFirstItem()->getDefaultTitle());
    }
 
    /**
     * Get data array for building filter items
     *
     * @return array
     */
    protected function _getItemsData()
    { 
    
        $products = $this->getLayer()->getProductCollection();
        $options = Mage::getModel('catalog/product_option')->getCollection()
          ->addFieldToFilter('option_code',$this->getOptionCode())
          ->addProductToFilter($products->getColumnValues('entity_id'))
        ;
        $this->_requestVar = $this->getOptionCode();
        $key = $this->getLayer()->getStateKey().'_'.$this->_requestVar;
        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        if ($data === null) {
          $data = array();
          $count = array();
          foreach($options as $option){
            $option_values = Mage::getModel('catalog/product_option_value')
              ->getValuesCollection($option)
              ->toArray()
            ;
            foreach ($option_values['items'] as $ov) {
              $id = empty($ov['sku'])? $ov['title']:$ov['sku'];
              if(!isset($count[$id])){
                $count[$id] = 1;
              }else{
                $count[$id]++;
              }
            }
            foreach ($option_values['items'] as $ov) {
            
              $id = empty($ov['sku'])? $ov['title']:$ov['sku'];
              if (Mage::helper('core/string')->strlen($id)) {
                $data[$id] = array(
                  'label' => $ov['title'],
                  'value' => $ov['option_id'].'_v_'.$id,
                  'count' => $count[$id],
                );
              }
            }
          }
          $tags = array(
              Mage_Eav_Model_Entity_Attribute::CACHE_TAG.':'.$key
          );
          $tags = $this->getLayer()->getStateTags($tags);
          $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }
    
    /**
     * set option code 
     *
     * @param   string $option_code
     * @return  Magetools_OptionFilter_Model_Layer_Option
     */
    public function setOptionCode($option_code)
    {
      $this->_requestVar = $option_code;
      $this->setData('option_code', $option_code);
      return $this;
    }
 
}
