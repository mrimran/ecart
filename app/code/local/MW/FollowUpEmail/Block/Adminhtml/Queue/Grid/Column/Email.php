<?php

class MW_FollowUpEmail_Block_Adminhtml_Queue_Grid_Column_Email extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text

{

    public function render(Varien_Object $row)

    {        

		$email = $row->getData($this->getColumn()->getIndex());		

		if($email != ""){

			$customer = Mage::getModel("customer/customer");		

			$customer->setWebsiteId(array(Mage::app()->getStore(true)->getWebsite()->getId()));

			$customer->loadByEmail($email); //load customer by email id						

			if($customer->getId() > 0){

				$url = $this->getUrl('*/customer/edit', array('id' => $customer->getId()));

				return "<a href='$url'>$email</a>";

			}
			else{
				return $email;
			}

		}

		return "";			    

    }

}		

		