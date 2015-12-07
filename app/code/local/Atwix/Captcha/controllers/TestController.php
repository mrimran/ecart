<?php
class Atwix_Captcha_TestController extends Mage_Core_Controller_Front_Action
{
    public function formAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}