 <?php 
class Tabs_Extension_Block_Ourcollection extends Mage_Catalog_Block_Product_Abstract {
  
 
  // function for displaying brands of category

 

    protected function getProductCollectionGroup()
    {
         if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                    ->setPage(1, 1)
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
        $this->_productCollection = $layer->getProductCollection();

       $this->_productCollection = Mage::getResourceModel('catalog/product_collection')
       ->addAttributeToSelect('*')
       ->addAttributeToFilter("type_id",array("eq"=>"grouped"));
       }
                           
        return $this->_productCollection;
    }

    protected function getProductCollectionProduct($productid)
    {
       $Product = Mage::getModel('catalog/product')->load($productid);
       $_testproductCollection = $Product->getTypeInstance(true)->getAssociatedProducts($Product);           
       return $_testproductCollection;
    }



   public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getProductCollectionGroup();

        // use sortable parameters
        $orders = array('entity_id' => $this->__('Latest'), 'price' => $this->__('Price') ); 
            $toolbar->setAvailableOrders($orders);
        
        if ($sort = $this->getSortBy()) {
            $toolbar->setAvailableOrders($orders);
            $toolbar->setDefaultOrder('entity_id');
            $toolbar->setDefaultDirection('desc');
        }
        if ($modes = $this->getModes()) {
        $toolbar->setModes($modes);
    }
 
    // set collection to tollbar and apply sort
    $toolbar->setCollection($collection);
 
    $this->setChild('toolbar', $toolbar);
    Mage::dispatchEvent('catalog_block_product_list_collection', array(
        'collection'=>$this->getProductCollectionGroup(),
    ));
 
    $this->getProductCollectionGroup()->load();
    Mage::getModel('review/review')->appendSummary($this->getProductCollectionGroup());
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

   
     public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('catalog/layer');
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
        $this->getProductCollectionGroup()->addAttributeToSelect($code);
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
            $this->getItemsTags($this->getProductCollectionGroup())
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
?>