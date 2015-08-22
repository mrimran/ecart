<?php



class MW_FollowUpEmail_Block_Adminhtml_Coupons_Grid extends Mage_Adminhtml_Block_Widget_Grid

{

  public function __construct()

  {

      parent::__construct();

      $this->setId('couponsGrid');

      $this->setDefaultSort('expiration_date');

      $this->setDefaultDir('DESC');

      $this->setSaveParametersInSession(true);	  
	   $this->setUseAjax(true);

  }



  protected function _prepareCollection()

  {

  	  //Mage::log(get_class(Mage::getModel('followupemail/rule')));

      $collection = Mage::getModel('followupemail/coupons')->getCollection();		
		$collection->getSelect()->joinLeft(array('e' => $collection->getTable('followupemail/emailqueue')),

                        'main_table.code=e.coupon_code',

                        array('e.queue_id','e.emailtemplate_id'));
		$collection->addFieldToFilter('e.status', array('neq' => 5));	  						
		$collection->getSelect()->joinLeft(array('r' => $collection->getTable('followupemail/rules')),

                        'e.rule_id=r.rule_id',

                        array('r.title', 'r.event'));									

      $this->setCollection($collection);
	 
	  	  
      return parent::_prepareCollection();

  }



  protected function _prepareColumns()

  {

      $this->addColumn('code', array(

          'header'    => Mage::helper('followupemail')->__('Coupon code'),
		  
          'width'     => '150px',

          'align'     =>'left',

          'index'     => 'code',

      ));
	  
	   $this->addColumn('sale_rule_id', array(

          'header'    => Mage::helper('followupemail')->__('Shopping Cart Rule'),

          'align'     =>'left',

          'index'     => 'sale_rule_id',

		  //'renderer' => 'MW_FollowUpEmail_Block_Adminhtml_Coupons_Grid_Column_Shoppingcartrule',
		  'type'    => 'options',

		  'options' => Mage::getSingleton('followupemail/system_config_shoppingcartrulegrid')->toOptionArray()

      ));

	  

	  $this->addColumn('created_at', array(

            'header'    => Mage::helper('followupemail')->__('Sent Date'),

            'align'     => 'left',

            'width'     => '150px',

            'type'      => 'datetime',

            'index'     => 'created_at',
			
			'empty_text' => Mage::helper('followupemail')->__('Not sent yet'),

			'renderer' => 'MW_FollowUpEmail_Block_Adminhtml_Coupons_Grid_Column_Emptydate',

        ));



    $this->addColumn('expiration_date', array(

        'header'    => Mage::helper('followupemail')->__('Expiration Date'),

        'align'     => 'left',

        'width'     => '150px',

        'type'      => 'datetime',

        'default'   => '--',

        'index'     => 'expiration_date',
		
		'empty_text' => Mage::helper('followupemail')->__('Not sent yet'),

		'renderer' => 'MW_FollowUpEmail_Block_Adminhtml_Coupons_Grid_Column_Emptydate',

    ));

		$this->addColumn('use_customer', array(

          'header'    => Mage::helper('followupemail')->__('Customer Email'),

          'align'     =>'left',

          'index'     => 'use_customer',

      ));
	  
	  $this->addColumn('title', array(

          'header'    => Mage::helper('followupemail')->__('FUE Rule'),

          'align'     =>'left',

          'index'     => 'title',

      ));
	  
	  $this->addColumn('event', array(

          'header'    => Mage::helper('followupemail')->__('Event'),

          'align'     =>'left',

          'index'     => 'event',

		  'type'    => 'options',

		  'options' => Mage::getSingleton('followupemail/system_config_eventfollowupemail')->toShortOptionArray(false)

      ));
	  
	  $this->addColumn('emailtemplate_id', array(

          'header'    => Mage::helper('followupemail')->__('Email Template'),

          'align'     =>'left',

          'index'     => 'emailtemplate_id',

      ));
	  
	  $this->addColumn('queue_id', array(

          'header'    => Mage::helper('followupemail')->__('Email Queue ID'),

          'align'     =>'left',

          'index'     => 'queue_id',

      ));

		$this->addColumn('coupon_status', array(

          'header'    => Mage::helper('followupemail')->__('Status'),

          'align'     => 'left',

          'width'     => '80px',

          'index'     => 'coupon_status',

          'type'      => 'options',

          'options'   => Mage::getModel('followupemail/system_config_statuscoupon')->toOptionArray()

      ));
	  
	

      
	  
	  

      return parent::_prepareColumns();

  }
  
  protected function _prepareMassaction()

    {

        $this->setMassactionIdField('coupon_id');
		
        $this->getMassactionBlock()->setFormFieldName('coupon');



        $this->getMassactionBlock()->addItem('delete', array(

             'label'    => Mage::helper('followupemail')->__('Delete'),

             'url'      => $this->getUrl('*/*/massDelete'),
            

        ));

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