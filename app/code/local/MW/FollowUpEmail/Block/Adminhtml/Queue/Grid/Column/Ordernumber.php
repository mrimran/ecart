<?php

class MW_FollowUpEmail_Block_Adminhtml_Queue_Grid_Column_Ordernumber extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text

{

    public function render(Varien_Object $row)

    {        

		$incrementId = $row->getData($this->getColumn()->getIndex());

		if($incrementId > 0){

			//$order = Mage::getModel('sales/order')->load($orderId);
			$order = Mage::getModel('sales/order')->load($incrementId, 'increment_id');

			$url =  $this->getUrl('*/sales_order/view', array('order_id' => $order->getId()));	

			//$orderNumber = $order->getIncrementId();

			return "<a href='$url'>$incrementId</a>";

		}

        return "";        

    }

}

