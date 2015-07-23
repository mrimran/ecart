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

class ISAAC_CatalogCategorySearch_Block_Form_Contents extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime'=> false,
            'cache_tags'    => array(Mage_Core_Model_Store::CACHE_TAG, Mage_Cms_Model_Block::CACHE_TAG)
        ));
    }

    public function getSearchableCategories()
    {
        $rootCategory = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId());
        return $this->getSearchableSubCategories($rootCategory);
    }

    public function getSearchableSubCategories($category)
    {
        return Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('all_children')
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('include_in_menu', 1)
            ->addIdFilter($category->getChildren())
            ->setOrder('position', 'ASC')
            ->load();
    }

}
