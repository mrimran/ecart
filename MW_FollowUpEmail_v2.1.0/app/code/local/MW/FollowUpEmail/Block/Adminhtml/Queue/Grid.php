<?php



class MW_FollowUpEmail_Block_Adminhtml_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid

{

  public function __construct()

  {

      parent::__construct();

      $this->setId('emailqueueGrid');

      $this->setDefaultSort('queue_id');

      $this->setDefaultDir('DESC');

      $this->setSaveParametersInSession(true);

	  $this->setUseAjax(true);

  }



  protected function _prepareCollection()

  {

  	  //Mage::log(get_class(Mage::getModel('followupemail/rule')));

      $collection = Mage::getModel('followupemail/emailqueue')->getCollection();
      $collection->addFieldToFilter('status',array('neq' => 5));
	  $collection->addFieldToFilter('recipient_email',array('neq' => ""));
      //foreach($collection->load()->getData() as $a ){
//          Mage::getModel('followupemail/emailqueue')->load($a['queue_id'])->delete();
//      }
	  $collection->getSelect()->joinLeft(array('r' => $collection->getTable('followupemail/rules')),

                        'main_table.rule_id=r.rule_id',

                        array('title', 'event'));										

		$collection->getSelect()->joinLeft(array('o' => $collection->getTable('sales/order')),

                        'main_table.order_id=o.entity_id',

                        array('increment_id'));

      $this->setCollection($collection);
      return parent::_prepareCollection();

  }



  protected function _prepareColumns()

  {

      $this->addColumn('queue_id', array(

          'header'    => Mage::helper('followupemail')->__('ID'),

          'align'     =>'right',

          'width'     => '50px',

          'index'     => 'queue_id',

      ));

	  

	  $this->addColumn('create_date', array(

            'header'    => Mage::helper('followupemail')->__('Create Date'),

            'align'     => 'left',

            'width'     => '120px',

            'type'      => 'datetime',

            'index'     => 'create_date',

			'renderer' => 'MW_FollowUpEmail_Block_Adminhtml_Queue_Grid_Column_Emptydate',

        ));



    $this->addColumn('scheduled_at', array(

        'header'    => Mage::helper('followupemail')->__('Scheduled At'),

        'align'     => 'left',

        'width'     => '120px',

        'type'      => 'datetime',

        'default'   => '--',

        'index'     => 'scheduled_at',

		'renderer' => 'MW_FollowUpEmail_Block_Adminhtml_Queue_Grid_Column_Emptydate',

    ));

		

	$this->addColumn('sent_at', array(

        'header'    => Mage::helper('followupemail')->__('Sent At'),

        'align'     => 'left',

        'width'     => '120px',

        'type'      => 'datetime',

        'default'   => '--',

        'index'     => 'sent_at',

		'empty_text' => Mage::helper('followupemail')->__('Not sent yet'),

        'renderer' => 'MW_FollowUpEmail_Block_Adminhtml_Queue_Grid_Column_Sentatemptydate',

    ));



		$this->addColumn('status', array(

          'header'    => Mage::helper('followupemail')->__('Status'),

          'align'     => 'left',

          'width'     => '80px',

          'index'     => 'status',

          'type'      => 'options',

          'options'   => Mage::getModel('followupemail/system_config_status')->toOptionArray()

      ));



		



      $this->addColumn('recipient_name', array(

          'header'    => Mage::helper('followupemail')->__('Customer Name'),

          'align'     =>'left',

          'index'     => 'recipient_name',

      ));

	  

	  $this->addColumn('recipient_email', array(

          'header'    => Mage::helper('followupemail')->__('Email'),

          'align'     =>'left',

          'index'     => 'recipient_email',

		  'renderer' => 'MW_FollowUpEmail_Block_Adminhtml_Queue_Grid_Column_Email',

      ));



	

	$this->addColumn('rule_title',

            array(

                'header' => Mage::helper('followupemail')->__('FUE Rule'),

                'align'  => 'left',

                'index'  => 'title',

            )

        ); 

		

	$this->addColumn('event',

            array(

                'header' => Mage::helper('followupemail')->__('Event'),

                'align'  => 'left',

                'index'  => 'event',

                'type'    => 'options',

                'options' => Mage::getSingleton('followupemail/system_config_eventfollowupemail')->toShortOptionArray(false)

            )

        );   

	

		$this->addColumn('emailtemplate_id',

            array(

                'header' => Mage::helper('followupemail')->__('Email Template'),

                'align'  => 'left',

                'index'  => 'emailtemplate_id',

            )

        );   			     

		

		$this->addColumn('increment_id',

            array(

                'header' => Mage::helper('followupemail')->__('Order Number'),

                'align'  => 'left',

                'index'  => 'increment_id',

				'renderer' => 'MW_FollowUpEmail_Block_Adminhtml_Queue_Grid_Column_Ordernumber',

            )

        );  			

		$this->addColumn('customer_response', array(

          'header'    => Mage::helper('followupemail')->__('Customer Response'),

          'align'     => 'left',

          'width'     => '80px',

          'index'     => 'customer_response',

          'type'      => 'options',

          'options'   => Mage::getModel('followupemail/system_config_response')->toOptionArray()

      ));

		$this->addColumn('action',

            array(

                'header'    =>  Mage::helper('followupemail')->__('Action'),

                'width'     => '80',

                'type'      => 'action',

                'getter'    => 'getId',

				'renderer' => 'MW_FollowUpEmail_Block_Adminhtml_Queue_Grid_Column_Actionbystatus',               

                'filter'    => false,

                'sortable'  => false,

                'index'     => 'stores',

                'is_system' => true,

        ));

				

	  

      return parent::_prepareColumns();

  }



    protected function _prepareMassaction()

    {

        $this->setMassactionIdField('queue_id');

        $this->getMassactionBlock()->setFormFieldName('emailqueue');

        $this->getMassactionBlock()->addItem('sendnow', array(

                'label'=> $this->__('Send now'),

                'url'  => $this->getUrl('*/*/massactionSend', array('_current'=>true)),

                'confirm'  => Mage::helper('followupemail')->__('Are you sure you want to do this?')

            )

        );

        $this->getMassactionBlock()->addItem('cancel', array(

                'label'=> $this->__('Cancel'),

                'url'  => $this->getUrl('*/*/massactionCancel', array('_current'=>true)),

                'confirm'  => Mage::helper('followupemail')->__('Are you sure you want to do this?')

            )

        );

        $this->getMassactionBlock()->addItem('delete', array(

                'label'=> $this->__('Delete'),

                'url'  => $this->getUrl('*/*/massactionDelete', array('_current'=>true)),

                'confirm'  => Mage::helper('followupemail')->__('Are you sure you want to do this?')

            )

        );

        return $this;

    }

	public function getRowValue($row)

  {	

		return $row->getId();//$this->getUrl('*/customer/edit', array('id' => $email));		

  }

	

  public function getRowUrl($row)

  {	

		return "";//$this->getUrl('*/customer/edit', array('id' => $email));		

  }

  

   public function getGridUrl()

    {

        return $this->getUrl('*/*/grid');

    }



}