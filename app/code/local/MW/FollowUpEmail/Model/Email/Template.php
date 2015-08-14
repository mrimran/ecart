<?php
class MW_FollowUpEmail_Model_Email_Template extends Mage_Core_Model_Email_Template{
	public function send($email, $name = null, array $variables = array())
    {				    
        if (!$this->isValidForSend()) {
            Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
            return false;
        }

        $emails = array_values((array)$email);
        $names = is_array($name) ? $name : (array)$name;
        $names = array_values($names);
        foreach ($emails as $key => $email) {
            if (!isset($names[$key])) {
                $names[$key] = substr($email, 0, strpos($email, '@'));
            }
        }

        $variables['email'] = reset($emails);
        $variables['name'] = reset($names);                        
        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $mail = $this->getMail();

        $setReturnPath = Mage::getStoreConfig(self::XML_PATH_SENDING_SET_RETURN_PATH);
        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = $this->getSenderEmail();
                break;
            case 2:
                $returnPathEmail = Mage::getStoreConfig(self::XML_PATH_SENDING_RETURN_PATH_EMAIL);
                break;
            default:
                $returnPathEmail = null;
                break;
        }

        if ($returnPathEmail !== null) {
            $mailTransport = new Zend_Mail_Transport_Sendmail("-f".$returnPathEmail);
            Zend_Mail::setDefaultTransport($mailTransport);
        }

        foreach ($emails as $key => $email) {
            $mail->addTo($email, '=?utf-8?B?' . base64_encode($names[$key]) . '?=');
        }

        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($variables, true);
		
		//add to links
		if(isset($variables['queueID']))
		$text = $this->formatUrlsInText($text,$variables['queueID']); 
	
        if($this->isPlain()) {
            $mail->setBodyText($text);
        } else {
            $mail->setBodyHTML($text);
        }

        $mail->setSubject('=?utf-8?B?' . base64_encode($this->getProcessedTemplateSubject($variables)) . '?=');
        $mail->setFrom($this->getSenderEmail(), $this->getSenderName());
		
        try {
            $mail->send();
            $this->_mail = null;
        }
        catch (Exception $e) {
            $this->_mail = null;
            Mage::logException($e);
            return false;
        }

        return true;
    }
	
	public function formatUrlsInText($text,$queueId){
		$text = ereg_replace( "www.", "http://www.", $text );
	    $text = ereg_replace( "http://http://www.", "http://www.", $text );
	    $text = ereg_replace( "https://http://www.", "https://www.", $text );
		
	    //$reg_exUrl = "/[-a-zA-Z0-9@:%_\+.~#?&\/\/=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)?/i";
		$reg_exUrl = "/<\s*a\s+[^>]*href\s*=\s*[\"']?([^\"' >]+)[\"' >]/";
	    if(preg_match_all($reg_exUrl, $text, $url)) {
	      
		   //print_r($url[1]);
	      $matches = ($url[1]);
		   
	       foreach($matches as $match) {	   			
					$find = $_SERVER['HTTP_HOST'];
					$pos = strpos($match, $find );
					if ($pos === false) {				    
					} else {
					    /*$strEnd = substr($match, -1);
						if($strEnd == "/") $replacement = $match . 'rid/100'.'"';
						else $replacement = $match  . '/rid/100'.'"';*/					
						$replacement = $this->add_var_to_url("qid",$queueId,$match).'"';
			            $text = str_replace($match.'"',$replacement,$text);
					}	            					
					//return $text;
			}
	       return $text;
	    }
		else {	               
	           return $text;

	    }                      
	}
	public function add_var_to_url($variable_name,$variable_value,$url_string){		
		if(strpos($url_string,"?")!==false){
			$start_pos = strpos($url_string,"?");
			$url_vars_strings = substr($url_string,$start_pos+1);
			$names_and_values = explode("&",$url_vars_strings);
			$url_string = substr($url_string,0,$start_pos);
			foreach($names_and_values as $value){
				list($var_name,$var_value)=explode("=",$value);
				if($var_name != $variable_name){
					if(strpos($url_string,"?")===false){
						$url_string.= "?";
					} else {
						$url_string.= "&";
					}
					$url_string.= $var_name."=".$var_value;
				}
			}
		} 		
		if(strpos($url_string,"?")===false){
			$url_string .= "?".$variable_name."=".$variable_value;
		} else {
			$url_string .= "&".$variable_name."=".$variable_value;
		}
		return $url_string;
	}
}