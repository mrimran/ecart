<?php

class TM_SmartSuggest_Model_Mysql4_Suggest extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_trackedEvents = array(
        'catalog_product_view',
        'catalog_product_compare_add_product',
        'checkout_cart_add_product',
        'wishlist_add_product'
    );

    protected function _construct()
    {
        $this->_init('core/website', 'website_id');
    }

    public function getTrackedEvents()
    {
        return $this->_trackedEvents;
    }

    /**
     * Retrieve recently viewed products, added to shopping cart,
     * to whishlist, to compare.
     *
     * @return array
     */
    public function getRecentlyIntrestedProductIds()
    {
        $subtype = 0;
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $subjectId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        } else {
            $subjectId = Mage::getSingleton('log/visitor')->getId();
            $subtype = 1;
        }

        $select = $this->_getReadAdapter()->select()
            ->from(array('report_event' => $this->getTable('reports/event')),
                array(
                    'logged_at' => 'logged_at',
                    'product_id' => 'object_id'//,
                    //'weight'        => new Zend_Db_Expr('COUNT(object_id)')
                )
            )
            ->joinInner(array('report_event_types' => $this->getTable('reports/event_type')),
                'report_event.event_type_id = report_event_types.event_type_id',
                'event_name'
            )
            ->joinInner(array('catalog_product_entity' => $this->getTable('catalog/product')),
                'report_event.object_id = catalog_product_entity.entity_id'
            )
            ->where('report_event.subject_id = ?', $subjectId)
            ->where('report_event_types.event_name IN (?)', $this->_trackedEvents)
            ->where('report_event.subtype = ?', $subtype)
            ->where('report_event.store_id = ?', Mage::app()->getStore()->getId())
            //->group('report_event_types.event_name')
            //->group('report_event.object_id')
            ->order('report_event.logged_at DESC')
            ->limit(10);

        return $this->_getReadAdapter()->fetchAll($select);
    }

    public function getPriceAttributeId()
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('attribute' => $this->getTable('eav/attribute'), 'attribute_id'))
            ->join(array('entity_type' => $this->getTable('eav/entity_type')),
                'attribute.entity_type_id = entity_type.entity_type_id'
            )
            ->where('attribute_code = ?', 'price')
            ->where('entity_type.entity_type_code = ?', 'catalog_product');

        return $this->_getReadAdapter()->fetchOne($select);
    }
}