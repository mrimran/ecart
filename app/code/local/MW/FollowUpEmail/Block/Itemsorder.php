<?php

class MW_FollowUpEmail_Block_Itemsorder extends Mage_Sales_Block_Items_Abstract

{

	protected $_order = null;

	

	protected function _construct()

    {

        parent::_construct();

        $this->setTemplate('mw_followupemail/itemsorder.phtml');

    }

	

	public function setOrder($order)

	{

		$this->_order = $order;

	}



	public function getOrder()

	{

		$order = $this->_order;

		return $order;

	}

	

	 public function getItemOptions($item)
    {
	
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
			/*if (isset($options['bundle_options'])) {
                $result = array_merge($result, $options['bundle_options']);
            }*/
        }
        return $result;
    }
	
	
	 public function getBundleOptions($item)
    {
        $options = $item->getProductOptions();
        if ($options) {
            if (isset($options['bundle_options'])) {
                return $options['bundle_options'];
            }
        }
        return array();
    }
	
	public function getFormatedOptionBundle($options){
		$result = array();
		if(is_array($options)){			
			$result['lable'] = $options['label'];
			foreach($options['value'] as $_value){
				$result['value'] = $_value['qty']." x ".$_value['title']." ".$this->getOrder()->formatPrice($_value['price']);
			}			
		}
		return $result;
	}
	 public function getFormatedOptionValue($optionValue)
    {
        $optionInfo = array();

        // define input data format
        if (is_array($optionValue)) {
            if (isset($optionValue['option_id'])) {
                $optionInfo = $optionValue;
                if (isset($optionInfo['value'])) {
                    $optionValue = $optionInfo['value'];
                }
            } elseif (isset($optionValue['value'])) {
                $optionValue = $optionValue['value'];
            }
        }

        // render customized option view
        if (isset($optionInfo['custom_view']) && $optionInfo['custom_view']) {
            $_default = array('value' => $optionValue);
            if (isset($optionInfo['option_type'])) {
                try {
                    $group = Mage::getModel('catalog/product_option')->groupFactory($optionInfo['option_type']);
                    return array('value' => $group->getCustomizedView($optionInfo));
                } catch (Exception $e) {
                    return $_default;
                }
            }
            return $_default;
        }

        // truncate standard view
        $result = array();
        if (is_array($optionValue)) {			
            $_truncatedValue = implode("\n", $optionValue);
            $_truncatedValue = nl2br($_truncatedValue);
            return array('value' => $_truncatedValue);
        } else {
            $_truncatedValue = Mage::helper('core/string')->truncate($optionValue, 55, '');
            $_truncatedValue = nl2br($_truncatedValue);
        }

        $result = array('value' => $_truncatedValue);

        if (Mage::helper('core/string')->strlen($optionValue) > 55) {
            $result['value'] = $result['value'] . ' <a href="#" class="dots" onclick="return false">...</a>';
            $optionValue = nl2br($optionValue);
            $result = array_merge($result, array('full_view' => $optionValue));
        }

        return $result;
    }

	/**

     * Retrieve order items collection

     *

     * @return unknown

     */

    public function getItemsCollection()

    {

        return $this->getOrder()->getItemsCollection();

    }

}