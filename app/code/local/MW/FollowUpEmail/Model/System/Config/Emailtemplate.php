<?php
class MW_FollowUpEmail_Model_System_Config_Emailtemplate
{
    const TEMPLATE_SOURCE_EMAIL        = 'email';
    const TEMPLATE_SOURCE_NEWSLETTER   = 'nsltr';
    const TEMPLATE_SOURCE_SEPARATOR    = ':';
    
    public function getEmailTemplates()
    {
        $templates = array();
        $templates[self::TEMPLATE_SOURCE_EMAIL] = Mage::helper('followupemail')->__('Email Templates');

        $templateArray = Mage::getResourceSingleton('core/email_template_collection')->toArray();
        foreach ($templateArray['items'] as $value)
            $templates[self::TEMPLATE_SOURCE_EMAIL.self::TEMPLATE_SOURCE_SEPARATOR.$value['template_id']] = $value['template_code'];

        $templates[self::TEMPLATE_SOURCE_NEWSLETTER] = Mage::helper('followupemail')->__('Newsletter Templates');

        $templateArray = Mage::getResourceModel('newsletter/template_collection')->load();
        foreach($templateArray as $item) 
            $templates[self::TEMPLATE_SOURCE_NEWSLETTER.self::TEMPLATE_SOURCE_SEPARATOR.$item->getData('template_id')] = $item->getData('template_code');

        return $templates;
    }

}