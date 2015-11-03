<?php
// app/code/local/Envato/Recentproducts/Block/Recentproducts.php
class Tabs_Extension_Block_Seller extends Mage_Core_Block_Template {
   protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
   
       public function getCategoryProducts($id){
        $memory = memory_get_usage();
        $time = microtime();
            /* @var $layer Mage_Catalog_Model_Layer */
            /* @var $layer Mage_Catalog_Model_Layer */
            //$this->_productCollection = $layer->getProductCollection();
            /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $category   = Mage::getModel('catalog/category')->load($this->_theCat);
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
        $collection->addFieldToFilter('status','1');
        //join brand 
           if($this->getRequest()->getParam('brands_ids')!= null AND $this->getRequest()->getParam('brands_ids')!= 0){
               $brand_id = $this->getRequest()->getParam('brands_ids'); 
               $condition = new Zend_Db_Expr("br.option_id = $brand_id AND br.product_ids = e.entity_id");
               $collection->getSelect()->join(array('br' => $collection->getTable('shopbybrand/brand')),
               $condition,
               array('brand_id' => 'br.option_id'));
        }
        $condition = new Zend_Db_Expr("e.entity_id = stock.product_id AND is_in_stock = 1");
            $collection->getSelect()->join(array('stock' => $collection->getTable('cataloginventory_stock_item')),
            $condition,
            array());
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
            $collection->getSelect()->where('c.entity_id = ?', $id)->limit(20);
            //$this->_productCollection->load();
        
        return $collection;
    }

       public function __construct(){

        parent::__construct();
        
        $storeId = Mage::app()->getStore()->getId();

        $products = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->addAttributeToSelect('id')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
           ->setPageSize(20);
           //->setCurPage(1)
           //->load();
             // most best sellers on top
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
        $this->setProductCollection($products);
       
         
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
