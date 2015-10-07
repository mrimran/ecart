<?php

class TM_SmartSuggest_Block_Suggest extends Mage_Catalog_Block_Product_Abstract
{
    const DEFAULT_PRODUCTS_COUNT = 6;
    const DEFAULT_COLUMN_COUNT   = 3;
    const DEFAULT_ORDER          = 'popularity';

    protected function _getProductCollection()
    {
        $collection = $this->_addProductAttributesAndPrices(
                Mage::getResourceModel('smartsuggest/reports_product_collection')
            )
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addAttributeToFilter(
                'entity_id',
                array(
                    'in' => Mage::getModel('smartsuggest/suggest')
                                ->getSuggestedProductIds()
                )
            )
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1);

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
        switch ($this->getOrder()) {
            case 'popularity':
                $collection->addViewsCount()
                    ->setOrder('views_count', 'desc');
                break;
            case 'sales':
                $collection->addOrderedQty()
                    ->addAttributeToSelect('ordered_qty')
                    ->setOrder('ordered_qty', 'desc');
                break;
            case 'random':
                $collection->getSelect()->order('RAND()');
                break;
            default:
                break;
        }

        $this->applyDefaultPriceBlock();
        $this->applyCategoryFilter($collection);
        return $collection;
    }

    /**
     * Prepare collection
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->_getProductCollection());
        return parent::_beforeToHtml();
    }

    public function applyCategoryFilter($collection)
    {
        if (null !== $this->category) {
            if ($this->category != 'current') {
                if (!is_numeric($this->category)) {
                    return;
                }
                $collection->addCategoryFilter(Mage::getModel('catalog/category')->load($this->category));
            } elseif ($category = Mage::registry('current_category')) {
                $collection->addCategoryFilter($category);
            }
        }
    }

    public function applyDefaultPriceBlock()
    {
        $this->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
    }

    public function getProductsCount()
    {
        if (null === $this->products_count) {
            $this->products_count = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->products_count;
    }

    public function getColumnCount()
    {
        if (null === $this->column_count) {
            $this->column_count = self::DEFAULT_COLUMN_COUNT;
        }
        return $this->column_count;
    }

    public function getOrder()
    {
        if (null === $this->order) {
            $this->order = self::DEFAULT_ORDER;
        }
        return $this->order;
    }

    public function getTitle()
    {
        if (null === $this->title) {
            $this->title = $this->__('Personalized recommendations');
        }
        return $this->title;
    }

    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        if (!$this->getData('enabled')) {
            return '';
        }
        $template = parent::getTemplate();
        if (!$template) {
            $template = $this->_getData('template');
        }
        return $template;
    }

    /**
     * Fill the block data with coniguration values
     *
     * @param string $path 'smartsuggest/left'
     */
    public function addDataFromConfig($path)
    {
        foreach (Mage::getStoreConfig($path) as $key => $value) {
            $this->setData($key, $value);
        }
        return $this;
    }

    /**
     * Set data using the Magento's configuration
     *
     * @param string $key
     * @param string $path
     * @return TM_SmartSuggest_Block_Suggest
     */
    public function setDataFromConfig($key, $path)
    {
        return $this->setData($key, Mage::getStoreConfig($path));
    }
}
