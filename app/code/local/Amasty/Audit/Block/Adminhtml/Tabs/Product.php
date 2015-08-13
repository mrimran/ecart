<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */


class Amasty_Audit_Block_Adminhtml_Tabs_Product extends Amasty_Audit_Block_Adminhtml_Tabs_DefaultItemColumns
{
    protected function _prepareCollection()
    {
        $elementId = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('amaudit/log')->getCollection();
        $collection->getSelect()
            ->joinLeft(array('r' => Mage::getSingleton('core/resource')->getTableName('amaudit/log_details')), 'main_table.entity_id = r.log_id', array('is_logged' => 'MAX(r.log_id)'))
            ->where("element_id = ?", $elementId)
            ->group('r.log_id')
        ;
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
}