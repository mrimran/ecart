 <?php 
class Tabs_Extension_Block_Category extends Mage_Catalog_Block_Product_Abstract {
  public function getLoadedProductCollectionbrand()
    {
         $id = '%7%';
         $collection = Mage::getModel('shopbybrand/brand')->getCollection()
        ->addFieldToSelect('*');
        $collection->getSelect()->order('brand_id ASC');
        $collection->getSelect()->where('category_ids LIKE ?', $id)->limit(5);
        return $collection;
    }
  
  public function getLoadedProductCollection()
    {
        $id = 7;
        Mage::getSingleton('core/session', array('name' => 'frontend'));
        $_productCollection = Mage::getResourceModel('catalogsearch/advanced_collection')
        ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
        ->addMinimalPrice()
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
}
?>