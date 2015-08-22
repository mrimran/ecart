<?php
/**
 * @category   Cactimedia
 * @package    Cactimedia_Cybersourcesa
 * @author     magepsycho@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Cactimedia_Cybersourcesa_Block_System_Config_Info extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<div style="background:url(\'http://www.cactimedia.com/images/cacti-logo-dubai.png\') no-repeat scroll 15px center #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 230px;">
                    <h4>About Cactimedia</h4>
                    <p>\'The best things come in small packages\' – or so they say. <br />
Cactimedia is a Dubai-based, boutique digital agency. Small, but perfectly formed.<br />
Our growth, from 2003 till now, means we\'re mature, and happy not to reach out too far.<br />
    That\'s because you don\'t outgrow boutique, and great acorns come from the best-tended oaks, not the largest. <br />
<br />
					Website: <a href="http://www.cactimedia.com" target="_blank">www.cactimedia.com</a></p>
                </div>';

        return $html;
    }
}
