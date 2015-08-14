<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
if ('true' == (string)Mage::getConfig()->getNode('modules/Amasty_Stockstatus/active'))
{
    class Amasty_Xnotif_Block_Product_View_Type_Grouped_Pure extends  Amasty_Stockstatus_Block_Rewrite_Product_View_Type_Grouped{}
}
else
{
    class Amasty_Xnotif_Block_Product_View_Type_Grouped_Pure extends Mage_Catalog_Block_Product_View_Type_Grouped {}
}
