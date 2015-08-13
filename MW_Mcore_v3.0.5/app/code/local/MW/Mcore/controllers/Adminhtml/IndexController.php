<?php
class MW_Mcore_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {    		
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function extendtrialAction()
    {       		    	 
    	$module = $this->getRequest()->getParam('module');       		
    	if(Mage::helper('mcore')->checkExistModule($module))    	
    	{				
    						
    						$strmcore = Mage::getStoreConfig(Mage::helper('mcore')->encryptModuleName($module));
    						$mod_infs_value =  Mage::helper('core')->decrypt($strmcore);
    						$mod_infs = explode(',',$mod_infs_value);
	    					if(intval($mod_infs[1])==0 || intval($mod_infs[1])==4 )
	    					{
	    						$timenow = strtotime(date('Y-m-d H:i:s'));
	    						$timeend = $timenow + Mage::helper('mcore')->timeExtendTrial();
	    						$mod_infs[2] = $timeend;  
	    						$mod_infs[1] = 1;
	    						$strmod = implode($mod_infs,',');
	    						$module_infs_value = Mage::helper('core')->encrypt($strmod);
	    						Mage::getModel('core/config')->saveConfig(Mage::helper('mcore')->encryptModuleName($module),$module_infs_value);
								Mage::getConfig()->reinit();
	    						Mage::helper('mcore')->enableConfig($module);	
	    						Mage::helper('mcore')->updatestatus($module,$timeend);		
	    						$result = 	Mage::helper('mcore')->getCommentExtendTrial($module,$timeend);	    						
	    						echo $result;	    						    						
	    					}
         }
         else
         {						
         						$mod_infs= array();
	    						$timenow = strtotime(date('Y-m-d H:i:s'));
	    						$timeend = $timenow + Mage::helper('mcore')->timeTrial();
	    						$mod_infs[2] = $timeend;  
	    						$mod_infs[1] = 1;
	    						$mod_infs[0] = $timenow;
	    						$strmod = implode($mod_infs,',');
	    						$module_infs_value = Mage::helper('core')->encrypt($strmod);
	    						Mage::getModel('core/config')->saveConfig(Mage::helper('mcore')->encryptModuleName($module),$module_infs_value);	
								Mage::getConfig()->reinit();
	    						Mage::helper('mcore')->enableConfig($module);
	    						Mage::helper('mcore')->updatestatus($module,$timeend);					
	    						echo Mage::helper('mcore')->getCommentExtendTrial($module,$timeend); 
         }   
			 
    	return;    	
    }
    
    public function trialAction()
    {
    	$modulename= $this->getRequest()->getParam('module');
    	$modules = Mage::helper('mcore')->getModules();    	
    	$module = ""; 
    	try {    	
	    	foreach ($modules as $row)
	    	{   	    				
	    		if(md5(strtolower($row).'/')==$modulename)
	    		{	    		
	    			$module = strtolower(str_replace('/','',$row));    		
	    		}    		
	    	}
	    	
	    	if(Mage::helper('mcore')->checkExistModule($module) && $module!="")    	
	    	{			
	    					$strmcore = Mage::getStoreConfig(Mage::helper('mcore')->encryptModuleName($module));
    						$mod_infs_value =  Mage::helper('core')->decrypt($strmcore);
    						$mod_infs = explode(',',$mod_infs_value);
	    					if(intval($mod_infs[1])==0 || intval($mod_infs[1])==4)
	    					{
	    						$timenow = strtotime(date('Y-m-d H:i:s'));
	    						$timeend = $timenow + Mage::helper('mcore')->timeExtendTrial();
	    						$mod_infs[2] = $timeend;  
	    						$mod_infs[1] = 1;
	    						$strmod = implode($mod_infs,',');
	    						$module_infs_value = Mage::helper('core')->encrypt($strmod);
	    						Mage::getModel('core/config')->saveConfig(Mage::helper('mcore')->encryptModuleName($module),$module_infs_value);	    						
								Mage::getConfig()->reinit();	    						   						
	    						Mage::helper('mcore')->enableConfig($module);	
	    						Mage::helper('mcore')->updatestatus($module,$timeend);	
	    						    						    						
	    					}
	         }
	         else if( $module!="" )
	         {					
         						// trial with data null (rarely happen)
         						$mod_infs= array();
	    						$timenow = strtotime(date('Y-m-d H:i:s'));
	    						$timeend = $timenow + Mage::helper('mcore')->timeTrial();
	    						$mod_infs[2] = $timeend;  
	    						$mod_infs[1] = 1;
	    						$mod_infs[0] = $timenow;
	    						$strmod = implode($mod_infs,',');
	    						$module_infs_value = Mage::helper('core')->encrypt($strmod);
	    						Mage::getModel('core/config')->saveConfig(Mage::helper('mcore')->encryptModuleName($module),$module_infs_value);	    						
								Mage::getConfig()->reinit();	    						 						
								Mage::helper('mcore')->enableConfig($module);		    									
	    						Mage::helper('mcore')->updatestatus($module,$timeend);
	         }  
	         $redirectUrl = Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit', array('section'=>'mcore'));
	         $this->_redirectUrl($redirectUrl);
			 return;
    	}
    	catch (Exception $e)
    	{    	
    	 $redirectUrl = Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit', array('section'=>'mcore'));
         $this->_redirectUrl($redirectUrl);
		 return;
    	}  
    }
        
    public function activeAction()
    {
    	try {
			 	$module = $this->getRequest()->getParam('module'); 			 	
				$orderid = $this->getRequest()->getParam('orderid');
				$type_site = $this->getRequest()->getParam('site');
				
				if(empty($type_site))
				$type_site = "live_site";

				$type_comment = Mage::helper("mcore")->getModuleTypeComment($module);
				
		    	if(!Mage::app()->getCookie()->get($module))
		    	{
		    		Mage::app()->getCookie()->set($module,'1',Mage::getStoreConfig('mcore/timelock'));
		    	}   
		    	else 
		    	{
		    		if(Mage::app()->getCookie()->get($module)< Mage::getStoreConfig('mcore/timestolock'))
		    		{
		    			Mage::app()->getCookie()->set($module,intval(Mage::app()->getCookie()->get($module))+1,Mage::getStoreConfig('mcore/timelock'));
		    		}
		    	} 	
		    if(Mage::app()->getCookie()->get($module)==Mage::getStoreConfig('mcore/timestolock'))
		    	{
		    		echo "You have tried to activate too many times. Please try again in next 60 minutes.";
			    	return;
		    	}
		    	else 
		    	{
    				$domain = Mage::getBaseUrl('link',Mage::getStoreConfig('web/secure/use_in_adminhtml')); //Mage::helper('mcore')->getDomain();	
	    			if(Mage::helper('mcore')->activeOnLocal($domain,$type_site))
	    			{    
    					echo "Can not activate on localhost.";
				    	return;	
	    			}
	    			else if(Mage::helper('mcore')->activeOnDevelopSite($domain,$type_site))
	    			{
	    				echo "Can not activate the extension on the development site.";
					    return;
	    			}
	    			else  
	    			{
	    				if($module!="" && $orderid!="" )
					    	{
					    		 $extend_name = Mage::helper('mcore')->getModuleEdition($module);
					    		 $newmodule = $module;
					    		 if(!empty($extend_name))
		 							$newmodule = $module.strtolower($extend_name);
		 							
					    		 if (class_exists('SoapClient'))
					    		 {
					    		 	$arr_info_api = array();
					    		 	$arr_info_api = array('module' =>$newmodule, 'orderid' =>$orderid,'domain'=>$domain,'type_site'=>$type_site,'module_system'=>$module,'comment'=>$type_comment);
					    		 	Mage::getModel('core/config')->saveConfig('mcore/errorSoap',0);							    		 	
									Mage::getConfig()->reinit();
									
						    		$client = new SoapClient(Mage::getStoreConfig('mcore/activelink'));								 	
						        	$session = $client->login(Mage::getStoreConfig('mcore/userapi'),Mage::getStoreConfig('mcore/codeapi'));
								    $result=$client->call($session,'managelicense.verifyPro',array($arr_info_api));	 
								    
								    Mage::helper('mcore')->getCommentActive($arr_info_api,$result);			
									echo $result[1];
					    		 }
					    		 else 
					    		 {			    		 	
					    		 	Mage::getModel('core/config')->saveConfig('mcore/errorSoap',1);
									Mage::getConfig()->reinit();
					    		 
					    		 	echo 'It requires to enable PHP SOAP extension to activate online. Or please go to <a href="http://www.mage-world.com/wiki/index.php?title=License-activation" target="_blank">this instruction</a> for generating the offline key.</div>';
					    		 }
					    	}
					    	else 
					    		echo "Can not connect to server because extension name or order number is null. Please try again later. ";
					    	return;
	    			}
		    	}
    	}
    	catch(Exception $e)
    	{
    		echo "Can not connect to server. Please try again later. Error message: ".$e;
    		return;
    	}
    }
    
     function hideAction()
    {
	    Mage::getSingleton('core/config')->saveConfig('mw/hidesoap',1);
		 Mage::getConfig()->reinit();	   
	    return true;
    }
    
    
   function remindAction()
    {
    	
    	$module =  $this->getRequest()->getParam('module'); 
    	$notification = Mage::getModel('mcore/notification')->load($module,"extension_key");
    	if($notification)
    	{
    		$notification->setStatus(1);
    		$notification->save();
    	}
    	
    	if(!$this->showMcoreNotification() && !$this->showMessage())
    		echo "hide";
    	else 
    		echo "nohide";
    	return;
    	
    }
    
   function notdisplayAction()
    {
    	$module =  $this->getRequest()->getParam('module'); 
   		$notification = Mage::getModel('mcore/notification')->load($module,"extension_key");
    	if($notification)
    	{
    		$notification->setStatus(2);
    		$notification->save();
    	}
    	if(!$this->showMcoreNotification() && !$this->showMessage())
    		echo "hide";
    	else 
    		echo "nohide";
    	return;
    	
    }
    
    function specnotdisplayAction()
    {
    	$id =  $this->getRequest()->getParam('id'); 
    	$spec_notice = Mage::getModel('mcore/notification')->load($id);
    	    	    	
    	if($spec_notice)
    		$spec_notice->setStatus(2);    	
    		$spec_notice->save();
	  
	  $this->removeMessageSession($id);
	  if(!$this->showMcoreNotification() && !$this->showMessage())
    	echo "hide";
      else 
    	echo "nohide";
       return;
    	
	    
    }
    
   function specremindAction()
    {
    	$id =  $this->getRequest()->getParam('id'); 
    	$spec_notice = Mage::getModel('mcore/notification')->load($id);    
    	if($spec_notice)
    	{	
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$resource = Mage::getSingleton('core/resource');
			$table_name = $resource->getTableName("mcore/notification");
			$sql = "update ".$table_name." set time_apply = DATE_ADD('".now()."',INTERVAL 1 DAY) where notification_id = ".$id;
			$write->query($sql);
    	} 
    	$this->removeMessageSession($id);
    	if(!$this->showMcoreNotification() && !$this->showMessage())
    		echo "hide";
    	else 
    		echo "nohide";
    	return;
    	
    }
        
	function activemanualAction()
	{
		$module = $this->getRequest()->getParam('module'); 
		$newmodule = $module;
		$keygen = $this->getRequest()->getParam('keygen');
		$type_site = $this->getRequest()->getParam('site');
		$domain = Mage::getBaseUrl('link',Mage::getStoreConfig('web/secure/use_in_adminhtml')); //Mage::helper('mcore')->getDomain();
		
		if(empty($type_site))
			$type_site = "live_site";
		
		$extend_name = Mage::helper('mcore')->getModuleEdition($module);
		if(!empty($extend_name))
		 $newmodule = $module.strtolower($extend_name);
		
		$arr_info_api = array('module' =>$newmodule, 'domain'=>$domain,'type_site'=>$type_site,'module_system'=>$module);
    		 		
		if(Mage::helper('mcore')->activeOnLocal($domain,$type_site))
	    	{    
    			echo "Can not activate on local host.";
				return;	
	    	}
	    	else if(Mage::helper('mcore')->activeOnDevelopSite($domain,$type_site))
	    	{
	    		echo "Can not activate the extension on the development site.";
				 return;
	    	}
	    	else  
	    	{					
				if($module!="" && $keygen !="")
				{
					Mage::helper('mcore')->getCommentActive($arr_info_api,$keygen);
				}
				else 
				{		
					echo "Activate failed. Please enter a valid activation key.";
				}
	    	}
	}
	
	function showMessage()
	{	
	  $notification = Mage::getModel('mcore/notification')->getCollection();
	  $notification->addFieldToFilter("current_display",1);
	  if($notification->getSize())		
	   return  true;
	  return  false;
	 	    
	}
	
	function removeMessageSession($id)
	{
		$notification = Mage::getModel('mcore/notification')->load($id);
		$notification->setCurrentDisplay(0);
		$notification->save();
	}
	
	function showMcoreNotification()
	{		
		$modulesCompany = Mage::helper("mcore")->getModuleCompany();
		$modulesCompany = array_map('strtolower', $modulesCompany);
	
		$mcore_notif = Mage::getModel('mcore/notification')->getCollection();
		$mcore_notif->addFieldToFilter('type',array("neq"=>'message'))
					->addFieldToFilter('extension_key',array("in"=>$modulesCompany))
					->addFieldToFilter('status',0);
							
		if($mcore_notif->getSize())
		{		
			return true;
		}
		else
		{
			
			return false;
		}
	}
	
  
}