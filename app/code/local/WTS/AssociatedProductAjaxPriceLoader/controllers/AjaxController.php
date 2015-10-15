<?php

/**
 * Description of AjaxController
 *
 * @author imran
 */
class WTS_AssociatedProductAjaxPriceLoader_AjaxController extends Mage_Core_Controller_Front_Action
{

    public function getAssociatedProductPriceAction()
    {
        $selectedAttrsAndVals = trim(Mage::app()->getRequest()->getParam('attrs_and_vals'));//Format:AttrID_AttrValId|AttrId_AttrValId
        echo $selectedAttrsAndVals;
        die();
        //we are going to break the above passed parameters and going to get the product based on the selection.
        /*$attributes = array(149 => 28, 150 => 32);
         $childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($attributes, $_product);
         $_childPrice = Mage::getModel('catalog/product')->load($childProduct->getEntityId())->getPrice();
          echo $_childPrice;
          echo '<pre>';
          print_r($childProduct);
          die;*/
    }

}
