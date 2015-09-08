 <?php 
class Tabs_Extension_Block_Category extends Mage_Catalog_Block_Product_Abstract {
  
 
  // function for displaying brands of category

  public function getLoadedProductCollectionbrand($cat_id)
    {
         $id = '%'.$cat_id.'%';
         $collection = Mage::getModel('shopbybrand/brand')->getCollection()
        ->addFieldToSelect('*');
        $collection->getSelect()->order('brand_id ASC');
        $collection->getSelect()->where('category_ids LIKE ?', $id)->limit(5);
        return $collection;
    }
  
   // function for displaying sale product of category

  public function getLoadedProductCollection($cat_id)
    {
        $id = $cat_id;
        Mage::getSingleton('core/session', array('name' => 'frontend'));
        $_productCollection = Mage::getResourceModel('catalogsearch/advanced_collection')
        ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
        ->addMinimalPrice()
        ->addAttributeToFilter('upcomingproduct', 0)
        ->addStoreFilter();

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

        if($categoryId = $id){
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $_productCollection->addCategoryFilter($category);
        } 
        
        return $_productCollection;
    }

     // function for displaying category of category
    
    public function getLoadedProductCollectionseller($cat_id)
    { 

      $id = $cat_id;
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
            $collection->getSelect()->where('c.parent_id = ?', $catId);
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
       
        return $collection;
        
    } 

     // function for displaying latest products of category

    protected function getProductCollectionLatest($category)
    {
 
       $_category = Mage::getModel('catalog/category')->load($category);

       $_testproductCollection = Mage::getResourceModel('catalog/product_collection')
       ->addCategoryFilter($_category)
       ->addAttributeToFilter('upcomingproduct', 0)
       ->addAttributeToSelect('*')
       ->setOrder('entity_id', 'desc')
       ->setPageSize(20);
                           
        return $_testproductCollection;
    }

    protected function getProductCollectionLatestpro()
    {
      
      $category = $this->getRequest()->getParam('cat_ids');
      $_category = Mage::getModel('catalog/category')->load($category);
      $_testproductCollection = Mage::getResourceModel('catalog/product_collection')
      ->addCategoryFilter($_category)
      ->addAttributeToSelect('*');
                           
      return $_testproductCollection;
    }
    
     // function for displaying best seller product  of category
    public function getLoadedProductCollectionsellers()
    { 

       $id = $this->getRequest()->getParam('cat_ids');
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
       
        return $collection;
        
    }
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
       
        return $collection;
        
    }

    protected function getProductCollectionUpcoming($category)
    {
 
       $_category = Mage::getModel('catalog/category')->load($category);

       $_testproductCollection = Mage::getResourceModel('catalog/product_collection')
       ->addCategoryFilter($_category)
       ->addAttributeToFilter('upcomingproduct', 1)
       ->addAttributeToSelect('*');
                           
        return $_testproductCollection;
    }  

    protected function getProductCollectionUpcomingPro()
    {
       $id = $this->getRequest()->getParam('cat_ids');
       
       $_category = Mage::getModel('catalog/category')->load($id);

       $_testproductCollection = Mage::getResourceModel('catalog/product_collection')
       ->addCategoryFilter($_category)
       ->addAttributeToFilter('upcomingproduct', 1)
       ->addAttributeToSelect('*');
                           
        return $_testproductCollection;
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
?>