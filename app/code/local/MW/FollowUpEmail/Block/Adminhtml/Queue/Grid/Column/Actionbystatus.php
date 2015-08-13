<?php

class MW_FollowUpEmail_Block_Adminhtml_Queue_Grid_Column_Actionbystatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text 

{

    public function render(Varien_Object $row)

    {

		$status = $row->getData("status");

		$queueId = $row->getData("queue_id");

		$urlPreview = $this->getUrl('*/*/preview', array('queue_id' => $queueId));

		$urlCancel = $this->getUrl('*/*/cancel', array('queue_id' => $queueId));

		$urlDelete = $this->getUrl('*/*/delete', array('queue_id' => $queueId));

		$urlSend = $this->getUrl('*/*/send', array('queue_id' => $queueId));

		if($status == MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_CANCELLED || $status == MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_SENT){

			$option = <<<EOD

				<select onchange="varienGridAction.execute(this);" class="action-select">

					<option value=""></option>

					<option value="{&quot;popup&quot;:&quot;1&quot;,&quot;href&quot;:&quot;$urlPreview/&quot;,&quot;onclick&quot;:&quot;popWin(this.href,'_blank','width=800,height=700,resizable=1,scrollbars=1');return false;&quot;}">Preview</option>	

					<option value="{&quot;confirm&quot;:&quot;Are you sure you want to delete the email ?&quot;,&quot;href&quot;:&quot;$urlDelete/&quot;}">Delete</option>		

				</select>

EOD;

		}		

		else{

			$option = <<<EOD

				<select onchange="varienGridAction.execute(this);" class="action-select">

					<option value=""></option>

					<option value="{&quot;popup&quot;:&quot;1&quot;,&quot;href&quot;:&quot;$urlPreview/&quot;,&quot;onclick&quot;:&quot;popWin(this.href,'_blank','width=800,height=700,resizable=1,scrollbars=1');return false;&quot;}">Preview</option>

					<option value="{&quot;confirm&quot;:&quot;Set the email status to 'Cancelled' ?&quot;,&quot;href&quot;:&quot;$urlCancel/&quot;}">Cancel</option>

					<option value="{&quot;confirm&quot;:&quot;Are you sure you want to delete the email ?&quot;,&quot;href&quot;:&quot;$urlDelete/&quot;}">Delete</option>

					<option value="{&quot;confirm&quot;:&quot;Are you sure you want to send the email immediately ?&quot;,&quot;href&quot;:&quot;$urlSend/&quot;}">Send now</option>

				</select>

EOD;

		}				

		return $option;

	}

}		

		