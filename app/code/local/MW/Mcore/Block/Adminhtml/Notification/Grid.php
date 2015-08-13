<?php

class MW_Mcore_Block_Adminhtml_Notification_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('notificationGrid');
      $this->setDefaultSort('notification_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('mcore/notification')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('notification_id', array(
          'header'    => Mage::helper('mcore')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'notification_id',
      ));

      $this->addColumn('type', array(
          'header'    => Mage::helper('mcore')->__('Type'),
          'align'     =>'left',
          'index'     => 'type',
      ));

     $this->addColumn('time_apply', array(
          'header'    => Mage::helper('mcore')->__('Start To Apply'),
          'align'     =>'left',
          'index'     => 'time_apply',
      ));
      

      
	  $this->addColumn('status', array(
          'header'    => Mage::helper('mcore')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
      		  0 => 'Normal',
              1 => 'Remind',
              2 => 'Not Display',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('mcore')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mcore')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('mcore')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('mcore')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('mcore_id');
        $this->getMassactionBlock()->setFormFieldName('mcore');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('mcore')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('mcore')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('mcore/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('mcore')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('mcore')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}