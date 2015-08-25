<?php

class TM_SmartSuggest_Model_Suggest extends Mage_Core_Model_Abstract
{
    /**
     * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected $_touchedProductsCollection = null;

    private $_priceId = null;

    protected function _construct()
    {
        $this->_init('smartsuggest/suggest');
        parent::_construct();
    }

    /**
     * Retrieve product ids that may be intresting for customer
     *
     * @return array
     */
    public function getSuggestedProductIds()
    {
        $products = $this->getTouchedProducts();
        $filters = $this->getFilters($products);

        // get related, upsell, crossell items
        // @todo filter by category, attribute_set_id and price range.
        $ids = array(
            'related'       => array(),
            'upsell'        => array(),
            'crossell'      => array(),
            'attribute_set' => array(),
            'category'      => array()
        );
        foreach ($this->_touchedProductsCollection->getItems() as $item) {
            if (!Mage::getStoreConfig('smartsuggest/source/related')
                && !Mage::getStoreConfig('smartsuggest/source/upsell')
                && !Mage::getStoreConfig('smartsuggest/source/crossell')) {

                break;
            }

            if (Mage::getStoreConfig('smartsuggest/source/related')) {
                $collection = $item->getRelatedProductCollection();
                $collection->getSelect()->limit(10);
                if (!empty($filters['viewed_product_ids'])) {
                    $collection->getSelect()->where('e.entity_id NOT IN (?)', $filters['viewed_product_ids']);
                }
                foreach ($collection->getItems() as $product) {
                    $ids['related'][$product->getEntityId()] = $product->getEntityId();
                }
            }
            if (Mage::getStoreConfig('smartsuggest/source/upsell')) {
                $collection = $item->getUpSellProductCollection();
                $collection->getSelect()->limit(10);
                if (!empty($filters['viewed_product_ids'])) {
                    $collection->getSelect()->where('e.entity_id NOT IN (?)', $filters['viewed_product_ids']);
                }
                $collection->load();
                Mage::dispatchEvent('catalog_product_upsell', array(
                    'product'       => $item,
                    'collection'    => $collection
                ));
                foreach ($collection->getItems() as $product) {
                    $ids['upsell'][$product->getEntityId()] = $product->getEntityId();
                }
            }
            if (Mage::getStoreConfig('smartsuggest/source/crossell')) {
                $collection = $item->getCrossSellProductCollection();
                $collection->getSelect()->limit(10);
                if (!empty($filters['viewed_product_ids'])) {
                    $collection->getSelect()->where('e.entity_id NOT IN (?)', $filters['viewed_product_ids']);
                }
                foreach ($collection->getItems() as $product) {
                    $ids['crossell'][$product->getEntityId()] = $product->getEntityId();
                }
            }
        }

        if (Mage::getStoreConfig('smartsuggest/source/attribute_set')) {
            foreach ($this->getAttributeSetBasedItems($filters) as $item) {
                $ids['attribute_set'][$item->getEntityId()] = $item->getEntityId();
            }
        }

        if (Mage::getStoreConfig('smartsuggest/source/category')) {
            foreach ($this->getCategoryBasedItems($filters) as $item) {
                $ids['category'][$item->getEntityId()] = $item->getEntityId();
            }
        }

        $result = array();
        foreach ($ids as $category => $productIds) {
            foreach ($productIds as $id) {
                $result[$id] = $id;
            }
        }

        return $result;
    }

    /**
     * Get products with the same attribute_set_id with similar price range
     * for each of sets
     *
     * @param array $filters
     * @return array
     */
    public function getAttributeSetBasedItems($filters)
    {
        if (!count($filters['attribute_set_id'])) {
            return array();
        }
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter(
                'attribute_set_id',
                array('in' => array_keys($filters['attribute_set_id']))
            );
        $where = $this->_getPriceWhere($filters['attribute_set_id'], 'attribute_set_id');
        $collection->getSelect()
            ->group('e.entity_id')
            ->order('RAND()')
            ->limit(10);

        if (!empty($where)) {
            if (null === $this->_priceId) {
                $this->_priceId = $this->getResource()->getPriceAttributeId();
            }
            $collection->getSelect()
                ->join(array('table_price' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_decimal')),
                    'table_price.entity_id = e.entity_id AND table_price.attribute_id = ' . $this->_priceId
                )
                ->where($where);
        }

        if (!empty($filters['viewed_product_ids'])) {
            $collection->getSelect()->where('e.entity_id NOT IN (?)', $filters['viewed_product_ids']);
        }

        return $collection->getItems();
    }

    /**
     * Get products from the same category with similar price range
     * fore each of category
     *
     * @param array $filters
     * @return array
     */
    public function getCategoryBasedItems($filters)
    {
        if (!count($filters['category_id'])) {
            return array();
        }
        $collection = Mage::getModel('catalog/product')->getCollection();
        $where = $this->_getPriceWhere($filters['category_id'], 'category_id');
        $collection->getSelect()
            ->group('e.entity_id')
            ->join(array('category_product' => $this->getResource()->getTable('catalog/category_product')),
                'category_product.product_id = e.entity_id'
            )
            ->where('category_product.category_id in (?)', array_keys($filters['category_id']))
            ->order('RAND()')
            ->limit(10);

        if (!empty($where)) {
            if (null === $this->_priceId) {
                $this->_priceId = $this->getResource()->getPriceAttributeId();
            }
            $collection->getSelect()
                ->join(array('table_price' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_decimal')),
                    'table_price.entity_id = e.entity_id AND table_price.attribute_id = ' . $this->_priceId
                )
                ->where($where);
        }

        if (!empty($filters['viewed_product_ids'])) {
            $collection->getSelect()->where('e.entity_id NOT IN (?)', $filters['viewed_product_ids']);
        }

        return $collection->getItems();
    }

    /**
     * Retrieve price where string
     *
     * @param array $filters
     * @param string $key
     * @return string
     */
    protected function _getPriceWhere($filters, $key)
    {
        if (!Mage::getStoreConfig('smartsuggest/filter/price')) {
            return '';
        }
        if ($key == 'category_id') {
            $idKey = 'category_product.category_id';
        } elseif ($key == 'attribute_set_id') {
            $idKey = 'e.attribute_set_id';
        } else {
            return '';
        }

        $where = '';
        $minRange = Mage::getStoreConfig('smartsuggest/filter/price_min_range');
        $maxRange = Mage::getStoreConfig('smartsuggest/filter/price_max_range');
        foreach ($filters as $id => $values) {
            if (!$id) {
                continue;
            }
            $values['prices'] = array_filter($values['prices']);
            if (count($values['prices'])) {
                $min = min($values['prices']) * ((100 - $minRange) / 100);
                $max = max($values['prices']) * ((100 + $maxRange) / 100);
                $where .= "({$idKey} = {$id} AND table_price.value >= {$min} AND table_price.value <= {$max} ) OR ";
            } else {
                $where .= "({$idKey} = {$id}) OR ";
            }
        }
        if (!empty($where)) {
            $where = substr($where, 0, -4);
        }
        return $where;
    }

    /**
     * Analyze and output most interested categories,
     * attribute_set_id
     * @todo consider to group type
     *
     * @param array $groups
     * 'group_key' => array(
     *  'product_id' => array(
     *      'product_id'        => int,
     *      'price'             => decimal,
     *      'attribute_set_id'  => int,
     *      'category_id'      => array,
     *      'weight'            => int
     * ))
     *
     * @return array
     * (
     *  'category_id' => array(id => array(prices)),
     *  'attribute_set_id' => array(id => array(prices))
     * )
     */
    public function getFilters(array $groups)
    {
        $attributeSetIds = array();
        $categoryIds = array();
        $productIds = array();

        foreach ($groups as $groupName => $products) {
            foreach ($products as $productId => $product) {
                $productIds[$productId] = $productId;
                // attribute_set_id
                if (!isset($attributeSetIds[$product['attribute_set_id']])) {
                    $attributeSetIds[$product['attribute_set_id']] = array(
                        'weight' => 0
                    );
                }
                //$attributeSetIds[$product['attribute_set_id']]['weight']++;
                $attributeSetIds[$product['attribute_set_id']]['weight'] += $product['weight'];

                // relation between attribute_set_id and price
                $attributeSetIds[$product['attribute_set_id']]['prices'][] = $product['price'];

                // category_id
                foreach ($product['category_id'] as $categoryId) {
                    if (!isset($categoryIds[$categoryId])) {
                        $categoryIds[$categoryId] = array(
                            'weight' => 0
                        );
                    }
                    //$categoryIds[$categoryId]['weight']++;
                    $categoryIds[$categoryId]['weight'] += $product['weight'];

                    // relation between category_id and price
                    $categoryIds[$categoryId]['prices'][] = $product['price'];
                }
            }
        }

        $result = array(
            'category_id'      => $categoryIds,
            'attribute_set_id' => $attributeSetIds
        );
        foreach ($result as &$array) {
            uasort($array, array($this, '_sortByWeight'));
            $this->_cutOffNonRelevantResults($array);
        }
        $this->_addNewcomingProducts($result, $groups);

        $result['viewed_product_ids'] = $productIds;
        return $result;
    }

    protected function _sortByWeight($a, $b)
    {
        if ($a['weight'] == $b['weight']) {
            return 0;
        }
        return ($a['weight'] > $b['weight']) ? -1 : 1;
    }

    protected function _cutOffNonRelevantResults(array &$array)
    {
        $flag = true;
        $first = current($array);
        do {
            end($array);
            if ($last = current($array)) {
                if ($last['weight'] == 0 || $first['weight']/$last['weight'] > 3) {
                    array_pop($array);
                } else {
                    $flag = false;
                }
            } else {
                $flag = false;
            }
        } while ($flag);
    }

    /**
     * Add 2 newcoming products, that hasn't enough weight to survive
     *
     * @param array $result
     * @param array $groups
     * @return void
     */
    protected function _addNewcomingProducts(array &$result, array $groups)
    {
        foreach ($this->getResource()->getTrackedEvents() as $event) {
            $i = 0;
            foreach ($groups[$event] as $id => $item) {
                if (!isset($result['attribute_set_id'][$item['attribute_set_id']])) {
                    $result['attribute_set_id'][$item['attribute_set_id']] = array(
                        'weight' => 1,
                        'prices' => array()
                    );
                }
                foreach ($item['category_id'] as $categoryId) {
                    if (!isset($result['category_id'][$categoryId])) {
                        $result['category_id'][$categoryId] = array(
                            'weight' => 1,
                            'prices' => array()
                        );
                    }
                }
                if ($i + $item['view_in_row'] >= 2) {
                    break;
                }
            }
        }
    }

    /**
     * Retrieve specific data from products, that were intrested to patricular customer.
     * Writes Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     *  to $_touchedProductsCollection
     *
     * @return array
     * (
     *  'catalog_product_view'          => array(),
     *  'catalog_product_comare_add'    => array(),
     *  'checkout_cart_add_product'     => array(),
     *  'wishlist_add_product'          => array(),
     *  'shopping_cart_product'         => array(),
     *  'wishlist_product'              => array()
     * )
     */
    public function getTouchedProducts()
    {
        $result = array();
        $ids = array();

        foreach (Mage::getModel('checkout/cart')->getQuoteProductIds() as $productId) {
            $ids[$productId] = $productId;
            $result['shopping_cart_product'][$productId] = array(
                'weight' => 1
            );
        }

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $result['wishlist_product'] = array();
            foreach (Mage::getModel('wishlist/wishlist')
                        ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer())
                        ->getProductCollection()
                        ->getItems() as $item) {

                $ids[$item->getProductId()] = $item->getProductId();
                $result['wishlist_product'][$item->getProductId()] = array(
                    'weight' => 1
                );
            }
        }

        // get recently viewed products, added to shopping cart, to whishlist, to compare.
        $lastViewed = false;
        $breakTheViewsInRow = array();
        foreach ($this->getResource()->getRecentlyIntrestedProductIds() as $item) {
            $ids[$item['product_id']] = $item['product_id'];
            $eventName = $item['event_name'];
            unset($item['event_name']);

            if (!isset($result[$eventName][$item['product_id']])) {
                $result[$eventName][$item['product_id']] = array(
                    'logged_at' => $item['logged_at'],
                    'product_id' => $item['product_id'],
                    'weight' => 0
                );
            }
            $result[$eventName][$item['product_id']]['weight']++;
            // calculating how many last views in a row of particular product we have
            if (!isset($result[$eventName][$item['product_id']]['view_in_row'])) {
                $result[$eventName][$item['product_id']]['view_in_row'] = 1;
            } elseif (false === $lastViewed
                || ($lastViewed == $item['product_id']
                    && !isset($breakTheViewsInRow[$item['product_id']]))) {

                $result[$eventName][$item['product_id']]['view_in_row']++;
            } else {
                $breakTheViewsInRow[$item['product_id']] = true;
            }
            $lastViewed = $item['product_id'];
        }

        // fill event recieved array with category_id, attribute_set_id, price
        $groups = $this->_getProductGroups();
        $this->_touchedProductsCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToFilter('entity_id', array('in' => $ids))
            ->addFinalPrice(); // comment this line for out of stock items

        foreach ($this->_touchedProductsCollection->getItems() as $item) {
            foreach ($groups as $group) {
                if (isset($result[$group]) && isset($result[$group][$item->getEntityId()])) {
                    $result[$group][$item->getEntityId()]['product_id']
                        = $item->getEntityId();

                    $price = $item->getFinalPrice();
                    if (!$price) {
                        $price = $item->getCalculatedFinalPrice();
                    }
                    if (!$price) {
                        $price = $item->getPrice();
                    }
                    $result[$group][$item->getEntityId()]['price'] = $price;

                    if ($item->getAttributeSetId()) {
                        $result[$group][$item->getEntityId()]['attribute_set_id']
                            = $item->getAttributeSetId();
                    }

                    /*
                    $categoryIds = $item->getCategoryIds();
                    $category = Mage::registry('current_category');
                    if (!$category || !in_array($category->getId(), $categoryIds)) {
                        $category = $item->getCategoryCollection()
                            ->setOrder('level', 'desc')
                            ->getFirstItem();
                    }
                    if ($category) {
                        $result[$group][$item->getEntityId()]['category_id']
                            = array($category->getId());
                    }
                    */

                    $result[$group][$item->getEntityId()]['category_id']
                        = $item->getCategoryIds();

                    $weightFactor = Mage::getStoreConfig("smartsuggest/event_importance/{$group}");
                    $result[$group][$item->getEntityId()]['weight']
                        *= is_numeric($weightFactor) ? $weightFactor : 1;
                }
            }
        }

        $result = array_merge(
            array_fill_keys($groups, array()),
            $result
        );

        return $result;
    }

    /**
     * @return array
     */
    protected function _getProductGroups()
    {
        return array_merge(array(
            'shopping_cart_product',
            'wishlist_product'
        ), $this->getResource()->getTrackedEvents());
    }
}
