<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
class Amasty_Xnotif_Block_Adminhtml_Catalog_Product_Edit_Tab_Alerts_Renderer_LastName extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    
   public function render(Varien_Object $row)
   {
       if(!$row->getEntityId()) {
             $row->setLastname($this->__('Guest'));
       }
       echo $row->getLastname();
   }
       
}