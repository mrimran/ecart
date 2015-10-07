<?php
// app/code/local/Envato/Recentproducts/Block/Recentproducts.php
class Tabs_Extension_Block_Seller extends Mage_Core_Block_Template {
   protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
   
       public function __construct(){

        parent::__construct();
   
        if($this->getRequest()->getParam('cat_id')== null){
        
        $storeId = Mage::app()->getStore()->getId();

        $products = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->addAttributeToSelect('id')
            ->addAttributeToSelect(array('name', 'price', 'small_image'))
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            //->setOrder('ordered_qty', 'desc')
            //->addAttributeToFilter('upcomingproduct', 0)
           ->setPageSize(54)
           ->load();
             // most best sellers on top
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
        $this->setProductCollection($products);
       }
       elseif($this->getRequest()->getParam('cat_id')!= null){
         $memory = memory_get_usage();
         $time = microtime();

        if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                    //->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                    $this->addModelTags($category);
                }
            }
            /* @var $layer Mage_Catalog_Model_Layer */
            /* @var $layer Mage_Catalog_Model_Layer */
            //$this->_productCollection = $layer->getProductCollection();
            /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
           $category   = Mage::getModel('catalog/category')->load($this->_theCat);
           $this->_productCollection = Mage::getResourceModel('catalog/product_collection');
           // join sales order items column and count sold products
        $expression = new Zend_Db_Expr("SUM(oi.qty_ordered)");
        $condition = new Zend_Db_Expr("e.entity_id = oi.product_id AND oi.parent_item_id IS NULL");
        $this->_productCollection->addAttributeToSelect('*')->getSelect()
            ->join(array('oi' => $this->_productCollection->getTable('sales/order_item')),
            $condition,
            array('sales_count' => $expression))
            ->group('e.entity_id');
            //->order('sales_count' . ' ' . 'desc');
            $this->_productCollection->addFieldToFilter('status','1');
        //join brand 
           if($this->getRequest()->getParam('brands_ids')!= null AND $this->getRequest()->getParam('brands_ids')!= 0){
               $brand_id = $this->getRequest()->getParam('brands_ids'); 
               $condition = new Zend_Db_Expr("br.option_id = $brand_id AND br.product_ids = e.entity_id");
               $this->_productCollection->getSelect()->join(array('br' => $this->_productCollection->getTable('shopbybrand/brand')),
               $condition,
               array('brand_id' => 'br.option_id'));
        }
        // join category
        $condition = new Zend_Db_Expr("e.entity_id = ccp.product_id");
        $condition2 = new Zend_Db_Expr("c.entity_id = ccp.category_id");
        $this->_productCollection->getSelect()->join(array('ccp' => $this->_productCollection->getTable('catalog/category_product')),
            $condition,
            array())->join(array('c' => $this->_productCollection->getTable('catalog/category')),
            $condition2,
            array('cat_id' => 'c.entity_id'));
        $condition = new Zend_Db_Expr("c.entity_id = cv.entity_id AND ea.attribute_id = cv.attribute_id");
        // cutting corners here by hardcoding 3 as Category Entiry_type_id
        $condition2 = new Zend_Db_Expr("ea.entity_type_id = 3 AND ea.attribute_code = 'name'");
        $this->_productCollection->getSelect()->join(array('ea' => $this->_productCollection->getTable('eav/attribute')),
            $condition2,
            array())->join(array('cv' => $this->_productCollection->getTable('catalog/category') . '_varchar'),
            $condition,
            array('cat_name' => 'cv.value'));
            $id = $this->getRequest()->getParam('cat_id');
            $this->_productCollection->getSelect()->where('c.entity_id = ?', $id)->limit(54);
            $this->_productCollection->load();
        
     }
        $this->setProductCollection($this->_productCollection);
    }

    }
    
    public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('catalog/layer');
    }

    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getProductCollection();

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
            'collection' => $this->getProductCollection()
        ));

        $this->getProductCollection()->load();

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
        $this->getProductCollection()->addAttributeToSelect($code);
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
            $this->getItemsTags($this->getProductCollection())
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

    public function getcategories(){
       $categories = Mage::getModel('catalog/category')->getCollection()
       ->addAttributeToSelect('*')//or you can just add some attributes
       ->addAttributeToFilter('level', 2)//2 is actually the first level
       ->addAttributeToFilter('is_active', 1)//if you want only active categories
       ;
       return $categories;
    }

    

}
