<?php
/**
 * @category   Cactimedia
 * @package    Cactimedia_Cybersourcesa
 * @author     magepsycho@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Cactimedia_Cybersourcesa_Block_Form extends Mage_Payment_Block_Form
{
	protected function _construct()
    {
        $this->setTemplate('cybersourcesa/form.phtml');
        parent::_construct();
    }
}
