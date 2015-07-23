<?php

/**
 * ISAAC Catalog Category Search
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    ISAAC
 * @package     ISAAC_CatalogCategorySearch
 * @copyright   Copyright (c) 2011 ISAAC Software Solutions B.V. (http://www.isaac.nl)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Aad Mathijssen
 * @version     1.0.0
 */

class ISAAC_CatalogCategorySearch_Block_Form extends Mage_Core_Block_Template
{
    /* gets the currently selected category id
     * 1) the active navigation category on category pages (depending on the configuration):
     * 1a) the active category navigation filter (if it is included in the categories dropdrown)
     * 1b) the current category from the main navigation
     * 2) the active category search filter on search results pages
     * 3) the root category on other pages
    **/
    public function getCurrentlySelectedCategoryId() {
        $helper = $this->helper('catalogcategorysearch');
        if ($helper->isCategoryPage() && $helper->selectCategoryOnCategoryPages()) {
            //find active category navigation filter
            foreach (Mage::getSingleton('catalog/layer')->getState()->getFilters() as $filterItem) {
                if ($filterItem->getFilter() instanceof Mage_Catalog_Model_Layer_Filter_Category) {
                    //only return the category id when it does not exceed the level of the categories that are shown
                    if ($filterItem->getFilter()->getCategory()->getLevel() <= $helper->getMaximumCategoryLevel()) {
                        return $filterItem->getValue();
                    }
                }
            }
            //get the current category from the main navigation
            return Mage::getSingleton('catalog/layer')->getCurrentCategory()->getEntityId();
        }
        if ($helper->isSearchResultsPage()) {
            //find first active category search filter
            foreach (Mage::getSingleton('catalogsearch/layer')->getState()->getFilters() as $filterItem) {
                 if ($filterItem->getFilter() instanceof Mage_Catalog_Model_Layer_Filter_Category) {
                     return $filterItem->getValue();
                 }
            }
        }
        //get the root category
        return Mage::app()->getStore()->getRootCategoryId();
    }
}
