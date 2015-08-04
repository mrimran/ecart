<?php
/**
 * @category   Cactimedia
 * @package    Cactimedia_Cybersourcesa
 * @author     magepsycho@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Cactimedia_Cybersourcesa_Block_System_Config_Payment extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
         $html = '<div style="background:#EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 10px;">
    <h4>About CyberSource Secure Acceptance WM</h4>
    <p><a href="http://www.cybersource.com/developers/learn/integration_methods/secure_acceptance_wm/" target="_blank">CyberSource Secure Acceptance Web/Mobile</a> allows businesses to accept payments made online, over the phone, and through mobile devices without ever handling toxic payment data, significantly reducing PCI DSS scope. It includes many additional benefits that reduce the burden and complexity of payment acceptance for IT departments and improves the checkout process for consumers.
<br />
<h4>CyberSource Secure Acceptance WM Configuration</h4>
<p>Go to System &raquo; Configuration &raquo; Sales &raquo; Payment Methods &raquo; CyberSource Secure Acceptance WM &raquo; Configure your settings here.</p>
</div>';

        return $html;
    }
}