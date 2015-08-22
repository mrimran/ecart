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
class Tabs_Extension_Block_Ajaxbestseller extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';

    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollectionpro($id)
    { 
       // benchmarking
        $memory = memory_get_usage();
        $time = microtime();
        $catId = $id;
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        // join sales order items column and count sold products
        $expression = new Zend_Db_Expr("SUM(oi.qty_ordered)");
        $condition = new Zend_Db_Expr("e.entity_id = oi.product_id AND oi.parent_item_id IS NULL");
        $collection->addAttributeToSelect('*')->getSelect()
            ->join(array('oi' => $collection->getTable('sales/order_item')),
            $condition,
            array('sales_count' => $expression))
            ->group('e.entity_id')
            ->order('sales_count' . ' ' . 'desc');
        //join brand 
           if($this->getRequest()->getParam('brands_ids')!= null AND $this->getRequest()->getParam('brands_ids')!= 0){
               $brand_id = $this->getRequest()->getParam('brands_ids'); 
               $condition = new Zend_Db_Expr("br.option_id = $brand_id AND br.product_ids = e.entity_id");
               $collection->getSelect()->join(array('br' => $collection->getTable('shopbybrand/brand')),
               $condition,
               array('brand_id' => 'br.option_id'));
        }
        // join category
        $condition = new Zend_Db_Expr("e.entity_id = ccp.product_id");
        $condition2 = new Zend_Db_Expr("c.entity_id = ccp.category_id");
        $collection->getSelect()->join(array('ccp' => $collection->getTable('catalog/category_product')),
            $condition,
            array())->join(array('c' => $collection->getTable('catalog/category')),
            $condition2,
            array('cat_id' => 'c.entity_id'));
        $condition = new Zend_Db_Expr("c.entity_id = cv.entity_id AND ea.attribute_id = cv.attribute_id");
        // cutting corners here by hardcoding 3 as Category Entiry_type_id
        $condition2 = new Zend_Db_Expr("ea.entity_type_id = 3 AND ea.attribute_code = 'name'");
        $collection->getSelect()->join(array('ea' => $collection->getTable('eav/attribute')),
            $condition2,
            array())->join(array('cv' => $collection->getTable('catalog/category') . '_varchar'),
            $condition,
            array('cat_name' => 'cv.value'));
        
        // if Category filter is on
        if ($catId) {
            $collection->getSelect()->where('c.entity_id = ?', $catId)->limit(20);
            
        }

        // unfortunately I cound not come up with the sql query that could grab only 1 bestseller for each category
        // so all sorting work lays on php
        $result = array();
        foreach ($collection as $product) {
            /** @var $product Mage_Catalog_Model_Product */
            if (isset($result[$product->getCatId()])) {
                continue;
            }
            $result[$product->getCatId()] = 'Category:' . $product->getCatName() . '; Product:' . $product->getName() . '; Sold Times:'. $product->getSalesCount();
        }
       
        $this->setProductCollection($collection);
        
    }
    /**
     * Get catalog layer model
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('catalog/layer');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->getLoadedProductCollectionpro($id);
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getLoadedProductCollectionpro($id);

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->getLoadedProductCollectionpro($id)
        ));

        $this->getLoadedProductCollectionpro($id)->load();

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

    public function addAttribute($code)
    {
        $this->getLoadedProductCollectionpro($id)->addAttributeToSelect($code);
        return $this;
    }

    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('catalog/config');
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Block_Product_List
     */
    public function prepareSortableFieldsByCategory($category) {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            if ($categorySortBy = $category->getDefaultSortBy()) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve block cache tags based on product collection
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(
            parent::getCacheTags(),
            $this->getItemsTags($this->getLoadedProductCollectionpro($id))
        );
    }
    
    public function getTotalOrder($id){
         $query = Mage::getResourceModel('sales/order_item_collection');
         $query->getSelect()->reset(Zend_Db_Select::COLUMNS)
         ->columns(array('sku','SUM(qty_ordered) as purchased'))
         ->group(array('sku'))
         ->where('product_id = ?',array($id))
         ->limit(1);
         return $query;
    }
    
}
