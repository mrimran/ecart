<?php

class MW_FollowUpEmail_Model_Validate

{

	protected function _compareValues($validatedValue, $value, $strict = true)

    {

        if ($strict && is_numeric($validatedValue) && is_numeric($value)) {

            return $validatedValue == $value;

        } else {

            $validatePattern = preg_quote($validatedValue, '~');

            if ($strict) {

                $validatePattern = '^' . $validatePattern . '$';

            }

            return (bool)preg_match('~' . $validatePattern . '~iu', $value);

        }

    }

	

	

	protected function validateAttribute($validatedValue,$op,$value)

    {

        if (is_object($validatedValue)) {

            return false;

        }



        $result = false;

        switch ($op) {

            case '==': case '!=':

                if (is_array($value)) {

                    if (is_array($validatedValue)) {

                        $result = array_intersect($value, $validatedValue);

                        $result = !empty($result);

                    } else {

                        return false;

                    }

                } else {

                    if (is_array($validatedValue)) {

                        $result = count($validatedValue) == 1 && array_shift($validatedValue) == $value;

                    } else {

                        $result = $this->_compareValues($validatedValue, $value);

                    }

                }

                break;



            case '<=': case '>':				

                if (!is_scalar($validatedValue)) {

                    return false;

                } else {

                    $result = $validatedValue <= $value;

                }

                break;



            case '>=': case '<':			

                if (!is_scalar($validatedValue)) {

                    return false;

                } else {

                    $result = $validatedValue >= $value;

                }

                break;



            case '{}': case '!{}':

                if (is_scalar($validatedValue) && is_array($value)) {

                    foreach ($value as $item) {

                        if (stripos($validatedValue,$item)!==false) {

                            $result = true;

                            break;

                        }

                    }

                }
				else if(is_scalar($value) && is_array($validatedValue)){
					
					foreach ($validatedValue as $item) {

                        if (stripos($item,$value)!==false) {

                            $result = true;

                            break;

                        }

                    }
				}
				elseif (is_array($value)) {

                    if (is_array($validatedValue)) {						

                        $result = array_intersect($value, $validatedValue);
						
						

                        $result = !empty($result);

                    } else {

                        return false;

                    }

                } else {

                    if (is_array($validatedValue)) {

                        $result = in_array($value, $validatedValue);

                    } else {

                        $result = $this->_compareValues($value, $validatedValue, false);

                    }

                }

                break;



            case '()': case '!()':

                if (is_array($validatedValue)) {

                    $result = count(array_intersect($validatedValue, (array)$value))>0;

                } else {

                    $value = (array)$value;

                    foreach ($value as $item) {

                        if ($this->_compareValues($validatedValue, $item)) {

                            $result = true;

                            break;

                        }

                    }

                }

                break;

        }



        if ('!=' == $op || '>' == $op || '<' == $op || '!{}' == $op || '!()' == $op) {

            $result = !$result;

        }



        return $result;

    }

	

    public function validate($condition,$order,$cart,$customerId){		

		//$validate = true;

		$product = Mage::getModel('catalog/product');

		$product_name = array();

		$product_price = array();

		$product_attribute_set = array();

		$tempCategoryIds = array();

		$categoryIds = array();		

		$sku = array();

		if($customerId != ""){

			$customerAddressId = Mage::getModel('customer/customer')->load($customerId)->getDefaultBilling();

			if ($customerAddressId){

				$address = Mage::getModel('customer/address')->load($customerAddressId);

				$city = $address->getData('city');

				$state = $address->getData('region');

				$zipcode = $address->getData('postcode');

				$country_id = $address->getData('country_id');

			}

		}

		if($order != null){

			$productIds = array();

			$productType = array();

			$items = $order->getAllItems();

			if($order->getPayment())

			$payment_method_code = $order->getPayment()->getMethodInstance()->getCode();		

			$shipping_method = $order->getData('shipping_method');	

			foreach($items as $item){

				$productIds[] = $item->getProductId();

				$sku[] = $item->getSku();			

	 			$product->unsetData()->load($item->getProductId());

				$product_name[] = $product->getName();

				$product_price[] = number_format($product->getPrice(), 2);

				$product_attribute_set[] = $product->getAttributeSetId();
				if(!$item->getParentItem())
				$productType[] = $product->getTypeId();

	            if (is_array($product->getCategoryIds()))

	                $tempCategoryIds[] .= implode(',', $product->getCategoryIds());

	            else $tempCategoryIds[] .= $product->getCategoryIds();

			}

			$billing_address = $order->getBillingAddress();

			if($city == "") $city = $billing_address->getCity();

			if($state == "") $state = $billing_address->getRegion();

			if($zipcode == "") $zipcode = $billing_address->getPostcode();

			if($country_id == "") $country_id = $billing_address->getCountryId();

		}

		if($cart != null){

			$subtotal = $cart['subtotal'];		

			$qty = number_format($cart['items_qty'],0);		

			$sku = explode(',',$cart['sku']);

			$productType = explode(',',$cart['product_type']);			

			$productIds = explode(',',$cart['product_ids']);

			if($city == "") $city = $cart['city'];

			if($state == "") $state = $cart['state'];

			if($zipcode == "") $zipcode = $cart['zipcode'];

			if($country_id == "") $country_id = $cart['country_id'];			

			

			foreach($productIds as $productId){

				$product->unsetData()->load($productId);	

				$product_name[] = $product->getName();

				$product_price[] = number_format($product->getPrice(), 2);

				$product_attribute_set[] = $product->getAttributeSetId();			

	            if (is_array($product->getCategoryIds()))

	                $tempCategoryIds[] .= implode(',', $product->getCategoryIds());

	            else $tempCategoryIds[] .= $product->getCategoryIds();	

			}			

		}

		foreach($tempCategoryIds as $cat){			

			$pos = strpos($cat, ',');			

			if ($pos === false) {			    

				$categoryIds[] = $cat;

			} else {

			    foreach(explode(',',$cat) as $c){

					$categoryIds[] = $c;

				}

			}			

		}		

		$validate = array();

		$i = 0;

		$isAll = true;		

		$isTrue = true;

		if(is_array($condition) && is_array($condition['conditions'])){

			if($condition['value'] == 0) $isTrue = false; 

			if($condition['aggregator'] == 'any') $isAll = false;

			foreach($condition['conditions'] as $cdt){				

				// Order

				if($order != null){
					
					if($cdt['attribute'] == 'order_subtotal'){					

						$validate[$i] = $this->validateAttribute($order->getSubtotal(),$cdt['operator'],$cdt['value']);

					}

									

					if($cdt['attribute'] == 'order_total_qty'){					

						$validate[$i] = $this->validateAttribute($order->getTotalQtyOrdered(),$cdt['operator'],$cdt['value']);					

					}

					

					if($cdt['attribute'] == 'order_payment_method'){

						$validate[$i] = $this->validateAttribute($payment_method_code,$cdt['operator'],$cdt['value']);

					}

					

					if($cdt['attribute'] == 'order_shipping_method'){

						$validate[$i] = $this->validateAttribute($shipping_method,$cdt['operator'],$cdt['value']);

					}	

				}				

			

				// Cart
				if($cart != null){
					if($cdt['attribute'] == 'cart_subtotal'){

						$validate[$i] = $this->validateAttribute($subtotal,$cdt['operator'],$cdt['value']);

					}				

					

					if($cdt['attribute'] == 'cart_total_qty'){

						$validate[$i] = $this->validateAttribute($qty,$cdt['operator'],$cdt['value']);

					}	
				}				
				
				if($cart != null || $order != null){
				
					// Product

					if($cdt['attribute'] == 'product_sku'){

						$skus_cdt = explode(',',$cdt['value']);	
						
						$validate[$i] = $this->validateAttribute($sku,$cdt['operator'],$skus_cdt);

					}				

					

					if($cdt['attribute'] == 'product_category_ids'){

						$categoryIds_cdt = explode(',',$cdt['value']);					

						$validate[$i] = $this->validateAttribute($categoryIds,$cdt['operator'],$categoryIds_cdt);

					}

					

					if($cdt['attribute'] == 'product_type'){

						$validate[$i] = $this->validateAttribute($productType,$cdt['operator'],$cdt['value']);

					}

					

					if($cdt['attribute'] == 'product_name'){

						$productName_cdt = $cdt['value'];							
						
						$validate[$i] = $this->validateAttribute($product_name,$cdt['operator'],$productName_cdt);

					}

					

					if($cdt['attribute'] == 'product_price'){

						$productPrice_cdt = explode(',',$cdt['value']);
						
						$validate[$i] = $this->validateAttribute($product_price,$cdt['operator'],$productPrice_cdt);			

					}

					

					if($cdt['attribute'] == 'product_attribute_set_id'){

						$validate[$i] = $this->validateAttribute($product_attribute_set,$cdt['operator'],$cdt['value']);

					}
				
				}
				// Customer

				if($cdt['attribute'] == 'city'){

					$citys_cdt = explode(',',$cdt['value']);					

					$validate[$i] = $this->validateAttribute(explode(',',$city),$cdt['operator'],$citys_cdt);					

				}	

				

				if($cdt['attribute'] == 'state'){

					$states_cdt = explode(',',$cdt['value']);					

					$validate[$i] = $this->validateAttribute(explode(',',$state),$cdt['operator'],$states_cdt);

				}	

				

				if($cdt['attribute'] == 'zipcode'){

					$zipcodes_cdt = explode(',',$cdt['value']);					

					$validate[$i] = $this->validateAttribute(explode(',',$zipcode),$cdt['operator'],$zipcodes_cdt);

				}	

				// Country

				if($cdt['attribute'] == 'country_id'){

					$validate[$i] = $this->validateAttribute($country_id,$cdt['operator'],$cdt['value']);

				}

				$i ++;

			}			

		}		

		$result = true;

		
		foreach($validate as $_valid){
			
			// check true - any or all

			if($isTrue){

				if($isAll){

					if($_valid) $result = true;

					else return false;

				}

				else{

					if($_valid) return true;				

				}	

			}	

			// check false - any or all

			else{

				if($isAll){

					if($_valid) return false;

					else $result = true;

				}

				else{

					if(!$_valid) return true;				

				}

			}		

		}				

		return $result;

	}

}