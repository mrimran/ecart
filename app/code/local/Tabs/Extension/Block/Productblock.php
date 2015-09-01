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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product list
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tabs_Extension_Block_Productblock extends Mage_Catalog_Block_Product_Abstract
{
  
    public function getTotalOrder($id){
         $query = Mage::getResourceModel('sales/order_item_collection');
         $query->getSelect()->reset(Zend_Db_Select::COLUMNS)
         ->columns(array('sku','SUM(qty_ordered) as purchased'))
         ->group(array('sku'))
         ->where('product_id = ?',array($id))
         ->limit(1);
         return $query;
    }
    
    public function getProductWishlist($id){
    	$wishlist = Mage::getModel('wishlist/item')->getCollection();
        $wishlist->getSelect()->where('main_table.product_id ='.$id);
        $count = $wishlist->count();
        return $count;

    }

    public function getTotalWishlist($id){
    	$wishlist = Mage::getModel('wishlist/item')->getCollection();
        $count = $wishlist->count();
        return $count;

    }

    public function showSummary($id){
    	$storeId = Mage::app()->getStore()->getId();
        $summaryData = Mage::getModel('review/review_summary')->setStoreId($storeId)->load($id);
        $Percent = $summaryData['rating_summary']; 
        $ratingPercent = ($Percent * 5)/100;
        return $ratingPercent;

    }
}
