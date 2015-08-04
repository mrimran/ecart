<?php
/**
 * @category   Cactimedia
 * @package    Cactimedia_Cybersourcesa
 * @author     magepsycho@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Cactimedia_Cybersourcesa_Block_Redirect extends Mage_Core_Block_Abstract
{
	protected function _toHtml()
	{
		$standard 	= $this->getOrder()->getPayment()->getMethodInstance();

        $form 		= new Varien_Data_Form();
        $form->setAction($standard->getCybersourcesaUrl())
            ->setId('cybersourcesa_payment_checkout')
            ->setName('cybersourcesa_payment_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);

		foreach ($standard->getFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to CyberSource Secure Acceptance WM in a few seconds.');
		$html.= $form->toHtml();
		#die($form->toHtml());
        $html.= '<script type="text/javascript">document.getElementById("cybersourcesa_payment_checkout").submit();</script>';
        $html.= '</body></html>';

		return $html;
    }
}