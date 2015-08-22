<?php

class TM_Core_Block_Adminhtml_Support_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('supportGrid');
        $this->setDefaultSort('modified_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection =  Mage::registry('tmcore_support_collection');
//        Zend_Debug::dump($collection->getFirstItem());
        if ($collection instanceof Varien_Data_Collection) {
            $this->setCollection($collection);
        }

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $model = Mage::registry('tmcore_support');

        $this->addColumn('id', array(
            'header'    => Mage::helper('helpmate')->__('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'id',
            'type'      => 'number',
//            'filter_condition_callback' => array($this, '_filterId'),
        ));

        $this->addColumn('text', array(
            'header'    => Mage::helper('helpmate')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
//            'filter_condition_callback' => array($this, '_filterTitle'),
        ));

        $this->addColumn('user_name', array(
            'header'    => Mage::helper('helpmate')->__('Assigned'),
            'align'     => 'left',
            'index'     => 'user_name',
//            'filter_condition_callback' => array($this, '_filterUserName'),
        ));

        $dapertments = array();
        if ($model->getDepartmets() instanceof Varien_Data_Collection) {
            $dapertments = $model->getDepartmets()->toOptionHash();
        }
        $this->addColumn('department', array(
            'header'    => Mage::helper('helpmate')->__('Department'),
            'align'     => 'left',
            'index'     => 'department_id',
            'type'      => 'options',
            'options'   => $dapertments,
//           'filter_condition_callback' => array($this, '_filterDepartamentId'),
        ));

        $priorities = array();
        if ($model->getPriorities() instanceof Varien_Data_Collection) {
            $priorities = $model->getPriorities()->toOptionHash();
        }
        $this->addColumn('priority', array(
            'header'         => Mage::helper('helpmate')->__('Priority'),
            'align'          => 'left',
            'width'          => '80px',
            'index'          => 'priority',
            'type'           => 'options',
            'options'        => $priorities,
            'frame_callback' => array($this, 'decorateStatus'),
//            'filter_condition_callback' => array($this, '_filterPriority'),
        ));

        $statuses = array();
        if ($model->getStatuses() instanceof Varien_Data_Collection) {
            $statuses = $model->getStatuses()->toOptionHash();
        }
        $this->addColumn('status', array(
            'header'  => Mage::helper('helpmate')->__('Status'),
            'align'   => 'left',
            'width'   => '80px',
            'index'   => 'status',
            'type'    => 'options',
            'options' => $statuses,
//            'filter_condition_callback' => array($this, '_filterStatus'),
        ));

        $this->addColumn('created_at', array(
            'header'        => Mage::helper('helpmate')->__('Created date'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
//            'filter_index'  => 'rt.created_at',
            'index'         => 'created_at',
//            'filter'        => false
        ));

        $this->addColumn('modified_at', array(
            'header'        => Mage::helper('helpmate')->__('Modified date'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
            'index'         => 'modified_at',
//            'filter'        => false
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('helpmate')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('helpmate')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('ticket_id' => $row->getId()));
    }

    /**
     * Decorate status column values
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $_classes = array('unknown', 'notice', 'minor', 'major', 'critical' , 'critical');
        $_class = isset($_classes[$row->priority]) ? $_classes[$row->priority] : 'unknown';

        return "<span class=\"grid-severity-{$_class}\"><span>{$value}</span></span>";
    }

    protected function _filterId($collection, $column)
    {
        $value = $column->getFilter()->getValue();
//        $collection->addFilter('department_id', $value);
        foreach ($collection as $item) {
            if ($item->id < $value['from'] || $item->id > $value['to']) {
                $collection->removeItemByKey($item->id);
            }
        }
    }

    protected function _filterTitle($collection, $column)
    {
        $value = $column->getFilter()->getValue();
//        $collection->addFilter('department_id', $value);
        foreach ($collection as $item) {
            if (false === strpos($item->title, $value)) {
                $collection->removeItemByKey($item->id);
            }
        }
    }

    protected function _filterUserName($collection, $column)
    {
        $value = $column->getFilter()->getValue();
//        $collection->addFilter('department_id', $value);
        foreach ($collection as $item) {
            if (false === strpos($item->user_name, $value)) {
                $collection->removeItemByKey($item->id);
            }
        }
    }

    protected function _filterDepartamentId($collection, $column)
    {
        $value = $column->getFilter()->getValue();
//        $collection->addFilter('department_id', $value);
        foreach ($collection as $item) {
            if ($item->department_id != $value) {
                $collection->removeItemByKey($item->id);
            }
        }
    }

    protected function _filterPriority($collection, $column)
    {
        $value = $column->getFilter()->getValue();
//        $collection->addFilter('department_id', $value);
        foreach ($collection as $item) {
            if ($item->priority != $value) {
                $collection->removeItemByKey($item->id);
            }
        }
    }

    protected function _filterStatus($collection, $column)
    {
        $value = $column->getFilter()->getValue();
//        $collection->addFilter('department_id', $value);
        foreach ($collection as $item) {
            if ($item->status != $value) {
                $collection->removeItemByKey($item->id);
            }
        }
    }
}