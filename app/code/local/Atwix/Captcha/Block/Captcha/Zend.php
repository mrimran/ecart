<?php
class Atwix_Captcha_Block_Captcha_Zend extends Mage_Captcha_Block_Captcha_Zend
{
    /**
     * Renders captcha HTML (if required)
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->getCaptchaModel()->generate();
 
        if (!$this->getTemplate()) {
            return '';
        }
        $html = $this->renderView();
 
        return $html;
    }
 
    /**
     * Returns URL to controller action which returns new captcha image
     *
     * @return string
     */
    public function getRefreshUrl()
    {
        return Mage::getUrl(
            Mage::app()->getStore()->isAdmin() ? 'adminhtml/refresh/refresh' : 'captcha/captcha/refresh',
            array('_secure' => Mage::app()->getStore()->isCurrentlySecure())
        );
    }
}