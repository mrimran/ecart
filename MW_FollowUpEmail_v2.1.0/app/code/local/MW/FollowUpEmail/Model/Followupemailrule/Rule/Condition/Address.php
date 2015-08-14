<?php

/**

 * Magento

 *

 * NOTICE OF LICENSE

 *

 * This source file is subject to the Open Software License (OSL 3.0)

 * that is bundled with this package in the file LICENSE.txt.

 * It is also available through the world-wide-web at this URL:

 * http://opensource.org/licenses/osl-3.0.php

 * If you did not receive a copy of the license and are unable to

 * obtain it through the world-wide-web, please send an email

 * to license@magentocommerce.com so we can send you a copy immediately.

 *

 * DISCLAIMER

 *

 * Do not edit or add to this file if you wish to upgrade Magento to newer

 * versions in the future. If you wish to customize Magento for your

 * needs please refer to http://www.magentocommerce.com for more information.

 *

 * @category    Mage

 * @package     Mage_SalesRule

 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)

 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

 */





class MW_FollowUpEmail_Model_Followupemailrule_Rule_Condition_Address extends Mage_Rule_Model_Condition_Abstract

{

	/*public function getDefaultOperatorInputByType()

    {

        if (null === $this->_defaultOperatorInputByType) {

            $this->_defaultOperatorInputByType = array(

                'string'      => array('==', '!=', '{}', '!{}', '()', '!()', 'REGEXP'),

                'numeric'     => array('==', '!=', '>=', '>', '<=', '<', '()', '!()'),

                'date'        => array('==', '>=', '<='),

                'select'      => array('==', '!='),

                'boolean'     => array('==', '!='),

                'multiselect' => array('{}', '!{}', '()', '!()'),

                'grid'        => array('()', '!()'),

				'message'     => array('{}', '!{}', 'REGEXP'),

            );

            $this->_arrayInputTypes = array('multiselect', 'grid');

        }

        return $this->_defaultOperatorInputByType;

    }*/



    /**

     * Default operator options getter

     * Provides all possible operator options

     *

     * @return array

     */

    public function getDefaultOperatorOptions()

    {

        if (null === $this->_defaultOperatorOptions) {

            $this->_defaultOperatorOptions = array(

                '=='  => Mage::helper('rule')->__('is'),

                '!='  => Mage::helper('rule')->__('is not'),

                '>='  => Mage::helper('rule')->__('equals or greater than'),

                '<='  => Mage::helper('rule')->__('equals or less than'),

                '>'   => Mage::helper('rule')->__('greater than'),

                '<'   => Mage::helper('rule')->__('less than'),

                '{}'  => Mage::helper('rule')->__('contains'),

                '!{}' => Mage::helper('rule')->__('does not contain'),

                '()'  => Mage::helper('rule')->__('is one of'),

                '!()' => Mage::helper('rule')->__('is not one of')

            	//'REGEXP' => Mage::helper('rule')->__('regular expression')

            );

        }

        return $this->_defaultOperatorOptions;

    }

	

	/**

     * Customize default operator input by type mapper for some types

     *

     * @return array

     */

    public function getDefaultOperatorInputByType()

    {

        if (null === $this->_defaultOperatorInputByType) {

            parent::getDefaultOperatorInputByType();

            /*

             * '{}' and '!{}' are left for back-compatibility and equal to '==' and '!='

             */

            $this->_defaultOperatorInputByType['category'] = array('==', '!=','{}', '!{}', '()', '!()');

            $this->_arrayInputTypes[] = 'category';

			

			$this->_defaultOperatorInputByType['numeric'] = array('==', '!=', '>=', '<=', '>', '<');

            $this->_arrayInputTypes[] = 'numeric';

        }

        return $this->_defaultOperatorInputByType;

    }

	

    public function loadAttributeOptions()

    {

        $attributes = array(       			

			'cart_subtotal' => Mage::helper('followupemail')->__('Subtotal'),

			'cart_total_qty' => Mage::helper('followupemail')->__('Total Items Quantity'),                      

			'order_status' => Mage::helper('followupemail')->__('Status'),

			'order_subtotal' => Mage::helper('followupemail')->__('Subtotal'),

			'order_total_qty' => Mage::helper('followupemail')->__('Total Items Quantity'),                                   

			'order_payment_method' => Mage::helper('salesrule')->__('Payment Method'),

            'order_shipping_method' => Mage::helper('salesrule')->__('Shipping Method'),

			'city' => Mage::helper('salesrule')->__('City'),

			'state' => Mage::helper('salesrule')->__('State'),

            'zipcode' => Mage::helper('salesrule')->__('Zip Code'),                        

            'country_id' => Mage::helper('salesrule')->__('Country'),

			'product_name' => Mage::helper('followupemail')->__('Name'),

			'product_sku' => Mage::helper('followupemail')->__('Sku'),

            'product_category_ids' => Mage::helper('followupemail')->__('Category'),

            'product_type' => Mage::helper('followupemail')->__('Type'), 

            'product_price' => Mage::helper('followupemail')->__('Price'), 

            'product_attribute_set_id' => Mage::helper('followupemail')->__('Attribute Set'), 

        );



        $this->setAttributeOption($attributes);



        return $this;

    }



    public function getAttributeElement()

    {

        $element = parent::getAttributeElement();

        $element->setShowAsText(true);

        return $element;

    }

	

	/**

     * Add special attributes

     *

     * @param array $attributes

     */

    protected function _addSpecialAttributes(array &$attributes)

    {

        $attributes['product_attribute_set_id'] = Mage::helper('catalogrule')->__('Attribute Set');

        $attributes['product_category_ids'] = Mage::helper('catalogrule')->__('Category');        

    }

	

	/**

     * Retrieve attribute object

     *

     * @return Mage_Catalog_Model_Resource_Eav_Attribute

     */

    public function getAttributeObject()

    {

        try {

            $obj = Mage::getSingleton('eav/config')

                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $this->getAttribute());

        }

        catch (Exception $e) {

            $obj = new Varien_Object();

            $obj->setEntity(Mage::getResourceSingleton('catalog/product'))

                ->setFrontendInput('text');

        }

        return $obj;

    }



    public function getInputType()

    {

		if ($this->getAttributeObject()->getAttributeCode() == 'product_category_ids') {

            return 'category';

        }

        switch ($this->getAttribute()) {

            case 'cart_subtotal': case 'order_subtotal': case 'cart_total_qty': case 'order_total_qty': case 'product_price':

                return 'numeric';



            case 'order_shipping_method': case 'order_payment_method': case 'country_id': case 'region_id': case 'status': case 'priority': case 'order_status': case 'product_type': case 'product_attribute_set_id':

                return 'select'; 

			

			case 'product_sku': case 'city': case 'state': case 'zipcode': case 'product_name':

                return 'category';            	 

        }

        return 'string';

    }



    public function getValueElementType()

    {

        switch ($this->getAttribute()) {

            case 'order_shipping_method': case 'order_payment_method': case 'country_id': case 'region_id': case 'status': case 'priority': case 'order_status': case 'product_type': case 'product_attribute_set_id':

                return 'select';

            case 'date(created_time)': case 'date(last_reply_time)':

            	return 'date';

        }

        return 'text';

    }



    public function getValueSelectOptions()

    {

        if (!$this->hasData('value_select_options')) {

			$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')

            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())

            ->load()

            ->toOptionHash();

            switch ($this->getAttribute()) {

                case 'country_id':

                    $options = Mage::getModel('adminhtml/system_config_source_country')

                        ->toOptionArray();

                    break;                              



                case 'region_id':

                    $options = Mage::getModel('adminhtml/system_config_source_allregion')

                        ->toOptionArray();

                    break;



                case 'order_shipping_method':

                    $options = Mage::getModel('adminhtml/system_config_source_shipping_allmethods')

                        ->toOptionArray();

                    break;



                case 'order_payment_method':

                    $options = Mage::getModel('adminhtml/system_config_source_payment_allmethods')

                        ->toOptionArray();

                    break;

				

				case 'order_status':

                    $options = Mage::getModel('followupemail/system_config_eventfollowupemail')

                        ->getOrderStatus();

                    break;

				

				case 'product_type':

                    $options = Mage::getModel('followupemail/system_config_producttypes')

                        ->getProductTypes();

                    break;

				case 'product_attribute_set_id':

                    $options = $sets;

                    break;

					

					



                default:

                    $options = array();

            }

            $this->setData('value_select_options', $options);

        }

        return $this->getData('value_select_options');

    }

	

	/**

     * Retrieve after element HTML

     *

     * @return string

     */

    public function getValueAfterElementHtml()

    {

        $html = '';



        switch ($this->getAttribute()) {

            case 'product_sku': case 'product_category_ids':

                $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');

                break;

        }



        if (!empty($image)) {

            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' . $image . '" alt="" class="v-middle rule-chooser-trigger" title="' . Mage::helper('rule')->__('Open Chooser') . '" /></a>';

        }

        return $html;

    }

	

	/**

     * Retrieve value element chooser URL

     *

     * @return string

     */

    public function getValueElementChooserUrl()

    {
        $url = false;

        switch ($this->getAttribute()) {

            case 'product_sku':

                $url = 'adminhtml/promo_widget/chooser'

                    .'/attribute/sku';

                //if ($this->getJsFormObject()) {

                    $url .= '/form/rule_conditions_fieldset';

               // }

                break;

			case 'product_category_ids':

                $url = 'adminhtml/promo_widget/chooser'

                    .'/attribute/category_ids';

               // if ($this->getJsFormObject()) {

                    $url .= '/form/rule_conditions_fieldset';

                //}

                break;

        }

        return $url!==false ? Mage::helper('adminhtml')->getUrl($url) : '';

    }

	

	/**

     * Retrieve Explicit Apply

     *

     * @return bool

     */

    public function getExplicitApply()

    {

        switch ($this->getAttribute()) {

            case 'product_sku': case 'product_category_ids':

                return true;

        }

        if (is_object($this->getAttributeObject())) {

            switch ($this->getAttributeObject()->getFrontendInput()) {

                case 'date':

                    return true;

            }

        }

        return false;

    }

	

	/**

     * Correct '==' and '!=' operators

     * Categories can't be equal because product is included categories selected by administrator and in their parents

     *

     * @return string

     */

    public function getOperatorForValidate()

    {

        $op = $this->getOperator();

        if ($this->getInputType() == 'product_category_ids') {

            if ($op == '==') {

                $op = '{}';

            } elseif ($op == '!=') {

                $op = '!{}';

            }

        }



        return $op;

    }





    /**

     * Validate Address Rule Condition

     *

     * @param Varien_Object $object

     * @return bool

     */ 

    public function validate(Varien_Object $object)

    {

        $address = $object;

        if (!$address instanceof Mage_Sales_Model_Quote_Address) {

            if ($object->getQuote()->isVirtual()) {

                $address = $object->getQuote()->getBillingAddress();

            }

            else {

                $address = $object->getQuote()->getShippingAddress();

            }

        }



        if ('payment_method' == $this->getAttribute() && ! $address->hasPaymentMethod()) {

            $address->setPaymentMethod($object->getQuote()->getPayment()->getMethod());

        }



        return parent::validate($address);

    }

}

