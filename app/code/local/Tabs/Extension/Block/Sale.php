<?php
class Tabs_Extension_Block_Sale extends Mage_Catalog_Block_Product_Abstract{
	
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
	public function getLoadedProductCollection()
	{
        Mage::getSingleton('core/session', array('name' => 'frontend'));
        $_productCollection = Mage::getResourceModel('catalogsearch/advanced_collection')
        ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
        ->addMinimalPrice()
        ->addStoreFilter()
        ->setPageSize(20)
        ->addAttributeToFilter('upcomingproduct', 0);
       
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($_productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($_productCollection);

        $todayDate = date('m/d/y');
        $tomorrow = mktime(0, 0, 0, date('m'), date('d'), date('y'));
        $tomorrowDate = date('m/d/y', $tomorrow);

        $_productCollection->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
        ->addAttributeToFilter('special_to_date', array('or'=> array(
        0 => array('date' => true, 'from' => $tomorrowDate),
        1 => array('is' => new Zend_Db_Expr('null')))
        ), 'left');

        return $_productCollection;
    }

    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getLoadedProductCollection();

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
            'collection' => $this->getLoadedProductCollection()
        ));

        $this->getLoadedProductCollection()->load();

        return parent::_beforeToHtml();
    }

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
        $this->getLoadedProductCollection()->addAttributeToSelect($code);
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
            $this->getItemsTags($this->getLoadedProductCollection())
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