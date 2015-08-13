<?php



class MW_FollowUpEmail_Block_Adminhtml_Rules_Grid extends Mage_Adminhtml_Block_Widget_Grid

{

  public function __construct()

  {

      parent::__construct();

      $this->setId('rulesGrid');

      $this->setDefaultSort('rule_id');

      $this->setDefaultDir('ASC');

      $this->setSaveParametersInSession(true);

  }

  protected function _prepareCollection()
  {
  	  //Mage::log(get_class(Mage::getModel('followupemail/rule')));

      $collection = Mage::getModel('followupemail/rules')->getCollection();

      $this->setCollection($collection);

      return parent::_prepareCollection();

  }



  protected function _prepareColumns()

  {

      $this->addColumn('rule_id', array(

          'header'    => Mage::helper('followupemail')->__('ID'),

          'align'     =>'right',

          'width'     => '50px',

          'index'     => 'rule_id',

      ));



      $this->addColumn('title', array(

          'header'    => Mage::helper('followupemail')->__('FUE Rule'),

          'align'     =>'left',

          'index'     => 'title',

      ));

	  

	  $this->addColumn('event', array(

          'header'    => Mage::helper('followupemail')->__('Event Trigger'),

          'align'     =>'left',

          'index'     => 'event',

		  'type'    => 'options',

		  'options' => Mage::getSingleton('followupemail/system_config_eventfollowupemail')->toShortOptionArray(false)

      ));



	$this->addColumn('from_date', array(

            'header'    => Mage::helper('followupemail')->__('From Date'),

            'align'     => 'left',

            'width'     => '120px',

            'type'      => 'datetime',

            'index'     => 'from_date',

        ));



        $this->addColumn('to_date', array(

            'header'    => Mage::helper('followupemail')->__('To Date'),

            'align'     => 'left',

            'width'     => '120px',

            'type'      => 'datetime',

            'default'   => '--',

            'index'     => 'to_date',

        ));

		

      $this->addColumn('is_active', array(

          'header'    => Mage::helper('followupemail')->__('Status'),

          'align'     => 'left',

          'width'     => '80px',

          'index'     => 'is_active',

          'type'      => 'options',

          'options'   => array(

              1 => 'Enabled',

              2 => 'Disabled',

          ),

      ));

	  

        $this->addColumn('action',

            array(

                'header'    =>  Mage::helper('followupemail')->__('Action'),

                'width'     => '100',

                'type'      => 'action',

                'getter'    => 'getId',

                'actions'   => array(

                    array(

                        'caption'   => Mage::helper('followupemail')->__('Edit'),

                        'url'       => array('base'=> '*/*/edit'),

                        'field'     => 'id'

                    )

                ),

                'filter'    => false,

                'sortable'  => false,

                'index'     => 'stores',

                'is_system' => true,

        ));

				

	  

      return parent::_prepareColumns();

  }



    protected function _prepareMassaction()

    {

        $this->setMassactionIdField('rule_id');
		
        $this->getMassactionBlock()->setFormFieldName('followupemail');



        $this->getMassactionBlock()->addItem('delete', array(

             'label'    => Mage::helper('followupemail')->__('Delete'),

             'url'      => $this->getUrl('*/*/massDelete'),

             'confirm'  => Mage::helper('followupemail')->__('The pending emails of the rules will be removed as well. Are you sure to delete?')

        ));



        $statuses = Mage::getSingleton('followupemail/status')->getOptionArray();



        array_unshift($statuses, array('label'=>'', 'value'=>''));

        $this->getMassactionBlock()->addItem('is_active', array(

             'label'=> Mage::helper('followupemail')->__('Change status'),

             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),

             'additional' => array(

                    'visibility' => array(

                         'name' => 'is_active',

                         'type' => 'select',

                         'class' => 'required-entry',

                         'label' => Mage::helper('followupemail')->__('Status'),

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