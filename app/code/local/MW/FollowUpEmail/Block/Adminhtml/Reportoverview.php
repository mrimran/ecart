<?php
class MW_FollowUpEmail_Block_Adminhtml_Reportoverview extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct(){
		$this->_controller = 'adminhtml_reportoverview';
		$this->_blockGroup = 'followupemail';
		$this->_headerText = Mage::helper('followupemail')->__('Report Overview');

		parent::__construct();
		$this->_removeButton('add');
	}
}