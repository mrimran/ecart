<?php
/**
 * @category   Cactimedia
 * @package    Cactimedia_Cybersourcesa
 * @author     magepsycho@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Cactimedia_Cybersourcesa_Model_System_Config_Source_Modes
{
    public function toOptionArray()
    {
        return array(
            0    => Mage::helper('cybersourcesa')->__('Test'),
            1    => Mage::helper('cybersourcesa')->__('Live'),
        );
    }
}