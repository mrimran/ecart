<?php
/**
 * @category   Cactimedia
 * @package    Cactimedia_Cybersourcesa
 * @author     magepsycho@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Cactimedia_Cybersourcesa_Block_Failure extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('cybersourcesa/failure.phtml');
    }

    /**
     * Get continue shopping url
     */
    public function getContinueShoppingUrl()
    {
        return Mage::getUrl('checkout/cart');
    }
}