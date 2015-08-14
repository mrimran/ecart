<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */

class  Amasty_Xnotif_Block_Adminhtml_Stock_Renderer_Website extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    
   public function render(Varien_Object $row)
   {
       $website = $row->getWebsiteId();
       $massName = Mage::getModel('core/website')->getCollection()->toOptionHash();
       
       $mass= explode(",", $website);
       foreach ($mass as $k => $v) {
           if(array_key_exists($v, $massName)) {
               $newmass[] = $massName[$v];    
           }
       }
       $website = implode(", ", array_unique($newmass)); 
       return $website;
   }
}
