<?php
class MW_FollowUpEmail_Model_Loadtemplate
{
	public function loademailtemplate(){			
		define('TEMPLATE_PREFIX', 'template="email:');
		try
		{
		    $filename = Mage::getModel('core/config')->getOptions()->getCodeDir() . DS . 'local' . DS . 'MW' . DS . 'FollowUpEmail' . DS . 'sql' . DS . 'followupemail_setup' . DS . 'emailtemplates' .DS. 'emailtemplates.xml';			
			$model = Mage::getModel('adminhtml/email_template');
			$templates = array();
		    $existingTemplates = array();
			
		    $templatesXml = simplexml_load_file($filename);

		    if (!$templatesXml) {		        
		        return;
		    }		    		    		   
		    
		    foreach ($templatesXml as $template) {
		        $dataTemp = array();
		        foreach ($template as $fieldName => $value) {
		            $dataTemp[$fieldName] = (string) $template->$fieldName;
		        }

		        if (!isset($dataTemp['template_code'])) continue;
		        if ($model->loadByCode($dataTemp['template_code'])->getId()) {
		            continue;
		        }

		        $templates[] = $dataTemp;
		    }
			$temId = 10;
		    foreach ($templates as $dataTemp) {
		        /*foreach ($existingTemplates as $k => $v) {
		            $dataTemp['template_text'] = str_replace(TEMPLATE_PREFIX . $k . '"', TEMPLATE_PREFIX . $k . '_' . $v . '"', $dataTemp['template_text']);
		        }*/

		        $model
		            ->setData($dataTemp)
		            /*->setTemplateId($temId)*/
		            ->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML)
		            ->setTemplateActual(1)
		            ->save();        
				$temId ++;
		    }
		} catch (Exception $e) {
		    Mage::logException($e);		
		}
	}
	
	public function getIdTemplateByCode($code = ""){
		$model = Mage::getModel('adminhtml/email_template');
		if ($model->loadByCode($code)->getId()) {
			return $model->loadByCode($code)->getId();
		}
		return 0;
	}
}