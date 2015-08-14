<?php



class MW_FollowUpEmail_Block_Adminhtml_Rules_Edit extends Mage_Adminhtml_Block_Widget_Form_Container

{

    public function __construct()

    {

        parent::__construct();

                 

        $this->_objectId = 'id';

        $this->_blockGroup = 'followupemail';

        $this->_controller = 'adminhtml_rules';
		$confirm = "";
        if( Mage::registry('rules_data') && Mage::registry('rules_data')->getId() ) {
		$ruleId = Mage::registry('rules_data')->getId();
		$queue = Mage::getModel('followupemail/emailqueue');		           				

		$queueEmails = $queue->getCollection()

			->addFieldToFilter('rule_id', $ruleId)

			->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);							

		$queueEmails->load();
		$count = count($queueEmails->getData());
		$confirm = Mage::helper('followupemail')->__("There are %d pending emails of this rule. Are you sure to remove all emails?",$count);
		}

        $this->_updateButton('save', 'label', Mage::helper('followupemail')->__('Save Rule'));
		
        $this->_updateButton('delete', 'label', Mage::helper('followupemail')->__('Delete Rule'));
		$this->_updateButton('delete','onclick', 'deleteConfirm(\''.$confirm.'\', \'' . $this->getDeleteUrl() . '\')');
			
		

        $this->_addButton('saveandcontinue', array(

            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),

            'onclick'   => 'saveAndContinueEdit()',

            'class'     => 'save',

        ), -100);

		

		/*$this->_addButton('saveandsendtest', array(

            'label'     => $this->__('Save And Send Test Email'),

            'onclick'   => 'saveAndSendTest()',

            'class'  => 'save'

        ), -200);*/		



        $this->_formScripts[] = <<<EOD
		
			Event.observe(window, 'load', function() {				
			    var addevent = false;
				var event = $('event').getValue();
				
				if(document.getElementById('rule_id') == null)
				{
				    $('applyoldback').style.display = 'none';
				    $('noteapplyoldback').style.display = 'none';
				}
				else{
					var ruleId = $('rule_id').getValue();
					if(ruleId == ""){
						$('applyoldback').style.display = 'none';
						$('noteapplyoldback').style.display = 'none';
					}
				}
				
				if( $('event').getValue() == 'customer_birthday' ){
					for(var i=0; i<maxItemsCount; i++)
			            if($('chain_row_'+i+'_BEFORE') != null)
			            {
			                $('chain_row_'+i+'_BEFORE').options[1].disabled = '';
			                $('chain_row_'+i+'_HOURS').style.display = 'none';
			                $('chain_row_'+i+'_MINUTES').style.display = 'none';
			            }
				}
							
				if(event == "new_customer_signed_up" || event == "customer_logged_in" || event == "customer_account_updated" || event == "customer_birthday"){
					$('applyoldback').style.display = 'none';
					$('noteapplyoldback').style.display = 'none';
				}
				
				
				
				
			})
			
			
            function toggleEditor() {

                if (tinyMCE.getInstanceById('followupemail_content') == null) {

                    tinyMCE.execCommand('mceAddControl', false, 'followupemail_content');

                } else {

                    tinyMCE.execCommand('mceRemoveControl', false, 'followupemail_content');

                }

            }

			function doBirthdayChanges(){				
			    if( $('event').getValue() == 'customer_birthday' )
			    {					
			        for(var i=0; i<maxItemsCount; i++)
			            if($('chain_row_'+i+'_BEFORE') != null)
			            {
			                $('chain_row_'+i+'_BEFORE').options[1].disabled = '';
			                $('chain_row_'+i+'_HOURS').style.display = 'none';
			                $('chain_row_'+i+'_MINUTES').style.display = 'none';
			            }
			    }
			    else
			    {					
			        for(var i=0; i<maxItemsCount; i++)
			            if($('chain_row_'+i+'_BEFORE') != null)
			            {
			                $('chain_row_'+i+'_BEFORE').value = $('chain_row_'+i+'_BEFORE').options[0].value;
			                $('chain_row_'+i+'_BEFORE').options[1].disabled = 'disabled';
			                $('chain_row_'+i+'_HOURS').style.display = '';
			                $('chain_row_'+i+'_MINUTES').style.display = '';
			            }
			    }
			}

			function sendTest(url){

				var param = $("edit_form").serialize();	        

				new Ajax.Request(url, {encoding:'UTF-8',method: 'POST',parameters: param,			

					onLoading : function(resp)

					{				

						//alert("doi");					

					},

					onSuccess : function(respjson)

					{

						var resp = respjson.responseText.evalJSON();		

						if(resp.err){

							alert(resp.mess);

						}

						else{

							alert(resp.mess);

						}

					}			

				}); 

			}

			
			function applyoldbackdata(url){

				var param = $("edit_form").serialize();	        

				new Ajax.Request(url, {encoding:'UTF-8',method: 'POST',parameters: param,			

					onLoading : function(resp)

					{				

						//alert("doi");					

					},

					onSuccess : function(respjson)

					{

						var resp = respjson.responseText.evalJSON();		

						if(resp.err){

							alert(resp.mess);

						}

						else{

							alert(resp.mess);

						}

					}			

				}); 

			}


            /*function saveAndContinueEdit(){

                editForm.submit($('edit_form').action+'back/edit/');

            }*/

			

			function doCheckEventType(){
				doBirthdayChanges();
				var event = $('event').getValue();
				if(event == "new_customer_signed_up" || event == "customer_logged_in" || event == "customer_account_updated" || event == "customer_birthday"){
					$('applyoldback').style.display = 'none';
					$('noteapplyoldback').style.display = 'none';
				}
				else{
					$('applyoldback').style.display = 'inline';
					$('noteapplyoldback').style.display = 'inline';
				}
				
				if(document.getElementById('rule_id') == null)
				{
				    $('applyoldback').style.display = 'none';
					$('noteapplyoldback').style.display = 'none';
				}
				else{
					var ruleId = $('rule_id').getValue();
					if(ruleId == ""){
						$('applyoldback').style.display = 'none';
						$('noteapplyoldback').style.display = 'none';
					}
				}				
                var n = event.search("cart_appeared");            
				if(n>0){

					var elSel = document.getElementById('cancel_event');					
                    					
					var i;

                    addevent = true;                               
					for (i = elSel.length - 1; i>=0; i--) {

						//alert(elSel.options[i].value);

						if (elSel.options[i].value == 'order_status_complete' || elSel.options[i].value == 'order_status_processing' || elSel.options[i].value == 'order_updated'|| elSel.options[i].value == 'order_status_closed'|| elSel.options[i].value == 'order_status_canceled'|| elSel.options[i].value == 'customer_logged_in'|| elSel.options[i].value == 'customer_account_updated'|| elSel.options[i].value == 'new_customer_signed_up') {

						  	elSel.remove(i);						  						 

						}																						

					}					

				}
                
                
				else{                                    
					var elSel1 = document.getElementById('cancel_event');					

					var i;
					
                    var events = [];
                    events.push({key:'order_status_complete',value:'Order Completed'});
                    events.push({key:'order_status_processing',value:'Order Processing'});
                    events.push({key:'order_updated',value:'Order Updated'});
                    events.push({key:'order_status_closed',value:'Order Closed'});
                    events.push({key:'order_status_canceled',value:'Order Cancelled'});
                    events.push({key:'customer_logged_in',value:'Customer Logged In'});
                    events.push({key:'customer_account_updated',value:'Customer Account Updated'});
                    events.push({key:'new_customer_signed_up',value:'New Customer Signed Up'});                                       
                    
					var e2 = document.getElementById('cancel_event');				
                    if(addevent){  
                        for (i = 0;i<events.length; i++) {
                        
                            var o = document.createElement('option');
                        
					        o.value = events[i].key;

					        o.text = events[i].value;

					        e2.options.add(o);
                            
                            addevent = false;
                        }
					}

				}

			}

EOD;

    }



    public function getHeaderText()

    {

        if( Mage::registry('rules_data') && Mage::registry('rules_data')->getId() ) {

            return Mage::helper('followupemail')->__("Edit Rule '%s'", $this->htmlEscape(Mage::registry('rules_data')->getTitle()));

        } else {

            return Mage::helper('followupemail')->__('Add Rule');

        }

    }

}