<?php

class TM_SmartSuggest_Model_Mysql4_Reports_Product_Collection
    extends Mage_Reports_Model_Mysql4_Product_Collection
{
    public function isEnabledFlat()
    {
        return false;
    }

    public function addOrderedQty($from = '', $to = '')
    {
        $adapter              = $this->getConnection();
//        $compositeTypeIds     = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');

        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),

        );

        $productJoinCondition = array(
//            $adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
            'pet.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );

        if ($from != '' && $to != '') {
            $fieldName            = $orderTableAliasName . '.created_at';
            $orderJoinCondition[] = $this->_prepareBetweenSql($fieldName, $from, $to);
        }

        $this->getSelect()//->reset()
            ->joinLeft(
                array('order_items' => $this->getTable('sales/order_item')),
                'e.entity_id = order_items.product_id',
                array(
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name'
                ))
            ->joinLeft(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array())
//            ->joinLeft(
//                array('pet' => $this->getProductEntityTableName()),
//                implode(' AND ', $productJoinCondition),
//                array(
//                    'entity_id'        => 'order_items.product_id',
//                    'entity_type_id'   => 'pet.entity_type_id',
//                    'attribute_set_id' => 'pet.attribute_set_id',
//                    'type_id'          => 'pet.type_id',
//                    'sku'              => 'pet.sku',
//                    'has_options'      => 'pet.has_options',
//                    'required_options' => 'pet.required_options',
//                    'created_at'       => 'pet.created_at',
//                    'updated_at'       => 'pet.updated_at'
//                ))
            ->where('parent_item_id IS NULL')
            ->group('e.entity_id')
//            ->having('SUM(order_items.qty_ordered) > ?', 0)
            ;

        return $this;

        /*$qtyOrderedTableName = $this->getTable('sales/order_item');
        $qtyOrderedFieldName = 'qty_ordered';

        $productIdTableName = $this->getTable('sales/order_item');
        $productIdFieldName = 'product_id';

        $compositeTypeIds = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        //$productTypes = $this->getConnection()->quoteInto(' AND (e.type_id NOT IN (?))', $compositeTypeIds);

        if ($from != '' && $to != '') {
            $dateFilter = " AND `order`.created_at BETWEEN '{$from}' AND '{$to}'";
        } else {
            $dateFilter = "";
        }

        $this->getSelect()->joinLeft(
            array('order_items' => $qtyOrderedTableName),
            'e.entity_id=order_items.product_id',
            array('ordered_qty' => "SUM(order_items.{$qtyOrderedFieldName})")
        );

        $order = Mage::getResourceSingleton('sales/order');
        $stateAttr = $order->getAttribute('state');
        if ($stateAttr->getBackend()->isStatic()) {

            $_joinCondition = $this->getConnection()->quoteInto(
                'order.entity_id = order_items.order_id AND order.state<>?', Mage_Sales_Model_Order::STATE_CANCELED
            );
            $_joinCondition .= $dateFilter;

            $this->getSelect()->joinLeft(
                array('order' => $this->getTable('sales/order')),
                $_joinCondition,
                array()
            );
        } else {
            $_joinCondition = 'order.entity_id = order_state.entity_id';
            $_joinCondition .= $this->getConnection()->quoteInto(' AND order_state.attribute_id=? ', $stateAttr->getId());
            $_joinCondition .= $this->getConnection()->quoteInto(' AND order_state.value<>? ', Mage_Sales_Model_Order::STATE_CANCELED);

            $this->getSelect()
                ->joinLeft(
                    array('order' => $this->getTable('sales/order')),
                    'order.entity_id = order_items.order_id' . $dateFilter,
                    array())
                ->joinLeft(
                    array('order_state' => $stateAttr->getBackend()->getTable()),
                    $_joinCondition,
                    array());
        }

        $this->getSelect()
            ->joinInner(array('pet' => $this->getProductEntityTableName()),
                "pet.entity_id = e.entity_id AND pet.entity_type_id = {$this->getProductEntityTypeId()}")
            ->group('pet.entity_id');

        return $this;
        */
    }

    public function addViewsCount($from = '', $to = '')
    {
        foreach (Mage::getModel('reports/event_type')->getCollection() as $eventType) {
            if ($eventType->getEventName() == 'catalog_product_view') {
                $productViewEvent = $eventType->getId();
                break;
            }
        }

        $this->getSelect()
            ->joinLeft(
                array('_table_views' => $this->getTable('reports/event')),
                'e.entity_id=_table_views.object_id AND _table_views.event_type_id = ' . $productViewEvent,
                array('views' => 'COUNT(_table_views.event_id)'))
            ->join(array('pet' => $this->getProductEntityTableName()),
                "pet.entity_id = e.entity_id AND pet.entity_type_id = {$this->getProductEntityTypeId()}")
            ->group('pet.entity_id')
            ->order('views desc');

        if ($from != '' && $to != '') {
            $this->getSelect()
                ->where('logged_at >= ?', $from)
                ->where('logged_at <= ?', $to);
        }

        return $this;
    }
}