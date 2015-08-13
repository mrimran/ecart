<?php

class MW_Mcore_Block_Adminhtml_System_Config_Extensioninfo extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

  public function getExtensionInfo()
  {
  		return  Mage::helper('mcore')->getModuleCompany();
  } 

  public function getStatus($modulename)
	{
			$module_infs_value = Mage::getStoreConfig(Mage::helper('mcore')->encryptModuleName($modulename));
			$module_inf = Mage::helper('core')->decrypt($module_infs_value); 
			$module_inf_arr = explode(',', $module_inf);
			if(is_array($module_inf_arr) && isset($module_inf_arr[1]))
				switch ($module_inf_arr[1])
				{
					case 1:
						return "trial";
					case 2:
						return "activated";
					case 0:
						return "disable";
					case 3:
						return "dev";
					case 4:
						return "devdis";
				}
			else 
				return "trial";
	}
}