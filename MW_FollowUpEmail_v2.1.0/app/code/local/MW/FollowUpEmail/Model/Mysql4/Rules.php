<?php
class MW_FollowUpEmail_Model_Mysql4_Rules extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the <module>_id refers to the key field in your database table.
        $this->_init('followupemail/rules', 'rule_id');
    }
	
	public function getTemplateContent($modelName, $templateName, $fieldNames = array(
        'subject' => 'template_subject',
        'content' => 'template_text',
        'sender_name' => 'template_sender_name',
        'sender_email' => 'template_sender_email',
        'template_styles' => 'template_styles',
        'code' => 'template_code',))
    {
        $db = $this->_getReadAdapter();

        $select = $db->select()
                    ->from($this->getTable($modelName), $fieldNames)
                    ->where('template_id=?', $templateName)
                    ->orwhere('template_code=?', $templateName)
                    ->limit(1);

        return $db->fetchRow($select);
    }
	
	public function getAllEmailRule($ruleId)
    {
        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from($this->getTable('followupemail/rules'), array(
                // 'sender_email', 'sender_name',
                    'copy_to_email',
                    'only_newsletter_subscribers',
                    'send_mail_customer'
                ))
            ->where('rule_id=?', $ruleId)
            ->limit(1);

        return $db->fetchRow($select);
    }
}