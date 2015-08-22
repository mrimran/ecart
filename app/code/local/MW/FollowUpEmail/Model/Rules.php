<?php

class MW_FollowUpEmail_Model_Rules extends Mage_Core_Model_Abstract
{
    
    protected $_conditions;
    
    
    
    protected $_actions;
    
    
    
    protected $_form;
    
    
    
    
    
    
    
    /**
    
    
    
    * Is model deleteable
    
    
    
    *
    
    
    
    * @var boolean
    
    
    
    */
    
    
    
    protected $_isDeleteable = true;
    
    
    
    
    
    
    
    /**
    
    
    
    * Is model readonly
    
    
    
    *
    
    
    
    * @var boolean
    
    
    
    */
    
    
    
    protected $_isReadonly = false;
    
    
    
    
    
    
    
    public function _construct()
    {
        
        
        
        parent::_construct();
        
        
        
        $this->_init('followupemail/rules');
        
        
        
    }
    
    
    
    
    
    
    
    //check date expire
    
    
    
    public function checkFromDateToDate($fromdate, $todate)
    {
        
        
        
        $todayDate = Mage::getModel('core/date')->timestamp(time());
        
        
        
        if ($fromdate != '' && $todate != '') {
            
            
            
            $dateStart = Mage::getSingleton('core/date')->timestamp($fromdate);
            
            
            
            $dateEnd = Mage::getSingleton('core/date')->timestamp($todate);
            
            
            
            if ($todayDate >= $dateStart && $todayDate <= $dateEnd) {
                
                
                
                return 1;
                
                
                
            }
            
            
            
        }
        
        
        
        return 0;
        
        
        
    }
    
    
    
    
    
    
    
    public function getConditionsInstance()
    {
        
        
        
        return Mage::getModel('followupemail/followupemailrule_rule_condition_combine');
        
        
        
    }
    
    
    
    
    
    
    
    public function _resetConditions($conditions = null)
    {
        
        
        
        if (is_null($conditions)) {
            
            
            
            $conditions = $this->getConditionsInstance();
            
            
            
        }
        
        
        
        $conditions->setRule($this)->setId('1')->setPrefix('conditions');
        
        
        
        $this->setConditions($conditions);
        
        
        
        
        
        
        
        return $this;
        
        
        
    }
    
    
    
    
    
    
    
    public function setConditions($conditions)
    {
        
        
        
        $this->_conditions = $conditions;
        
        
        
        return $this;
        
        
        
    }
    
    
    
    
    
    
    
    /**
    
    
    
    * Retrieve Condition model
    
    
    
    *
    
    
    
    * @return Mage_SalesRule_Model_Rule_Condition_Abstract
    
    
    
    */
    
    
    
    
    
    
    
    public function getConditions()
    {
        
        
        
        if (empty($this->_conditions)) {
            
            
            
            $this->_resetConditions();
            
            
            
        }
        
        
        
        return $this->_conditions;
        
        
        
    }
    
    
    
    
    
    
    
    public function getActionsInstance()
    {
        
        
        
        //return Mage::getModel('rule/action_collection');
        
        
        
        return Mage::getModel('followupemail/followupemailrule_rule_condition_product_combine');
        
        
        
    }
    
    
    
    
    
    
    
    public function _resetActions($actions = null)
    {
        
        
        
        if (is_null($actions)) {
            
            
            
            $actions = $this->getActionsInstance();
            
            
            
        }
        
        
        
        $actions->setRule($this)->setId('1')->setPrefix('actions');
        
        
        
        $this->setActions($actions);
        
        
        
        
        
        
        
        return $this;
        
        
        
    }
    
    
    
    
    
    
    
    public function setActions($actions)
    {
        
        
        
        $this->_actions = $actions;
        
        
        
        return $this;
        
        
        
    }
    
    
    
    
    
    
    
    public function getActions()
    {
        
        
        
        if (!$this->_actions) {
            
            
            
            $this->_resetActions();
            
            
            
        }
        
        
        
        return $this->_actions;
        
        
        
    }
    
    
    
    
    
    
    
    public function getForm()
    {
        
        
        
        if (!$this->_form) {
            
            
            
            $this->_form = new Varien_Data_Form();
            
            
            
        }
        
        
        
        return $this->_form;
        
        
        
    }
    
    
    
    /*
    
    
    
    public function asString($format='')
    
    
    
    {
    
    
    
    $str = Mage::helper('rule')->__("Name: %s", $this->getName()) ."\n"
    
    
    
    . Mage::helper('rule')->__("Start at: %s", $this->getStartAt()) ."\n"
    
    
    
    . Mage::helper('rule')->__("Expire at: %s", $this->getExpireAt()) ."\n"
    
    
    
    . Mage::helper('rule')->__("Description: %s", $this->getDescription()) ."\n\n"
    
    
    
    . $this->getConditions()->asStringRecursive() ."\n\n"
    
    
    
    . $this->getActions()->asStringRecursive() ."\n\n";
    
    
    
    return $str;
    
    
    
    }
    
    
    
    
    
    
    
    public function asHtml()
    
    
    
    {
    
    
    
    $str = Mage::helper('rule')->__("Name: %s", $this->getName()) ."<br/>"
    
    
    
    . Mage::helper('rule')->__("Start at: %s", $this->getStartAt()) ."<br/>"
    
    
    
    . Mage::helper('rule')->__("Expire at: %s", $this->getExpireAt()) ."<br/>"
    
    
    
    . Mage::helper('rule')->__("Description: %s", $this->getDescription()) .'<br/>'
    
    
    
    . '<ul class="rule-conditions">'.$this->getConditions()->asHtmlRecursive().'</ul>'
    
    
    
    . '<ul class="rule-actions">'.$this->getActions()->asHtmlRecursive()."</ul>";
    
    
    
    return $str;
    
    
    
    }
    
    
    
    */
    
    
    
    public function loadPost(array $rule)
    {
        
        
        
        $arr = $this->_convertFlatToRecursive($rule);
        
        
        
        if (isset($arr['conditions'])) {
            
            
            
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
            
            
            
        }
        
        
        
        if (isset($arr['actions'])) {
            
            
            
            $this->getActions()->setActions(array())->loadArray($arr['actions'][1], 'actions');
            
            
            
        }
        
        
        
        
        
        
        
        return $this;
        
        
        
    }
    
    
    
    
    
    
    
    protected function _convertFlatToRecursive(array $rule)
    {
        
        
        
        $arr = array();
        
        
        
        foreach ($rule as $key => $value) {
            
            
            
            if (($key === 'conditions' || $key === 'actions') && is_array($value)) {
                
                
                
                foreach ($value as $id => $data) {
                    
                    
                    
                    $path = explode('--', $id);
                    
                    
                    
                    $node =& $arr;
                    
                    
                    
                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        
                        
                        
                        if (!isset($node[$key][$path[$i]])) {
                            
                            
                            
                            $node[$key][$path[$i]] = array();
                            
                            
                            
                        }
                        
                        
                        
                        $node =& $node[$key][$path[$i]];
                        
                        
                        
                    }
                    
                    
                    
                    foreach ($data as $k => $v) {
                        
                        
                        
                        $node[$k] = $v;
                        
                        
                        
                    }
                    
                    
                    
                }
                
                
                
            } else {
                
                
                
                /**
                
                
                
                * convert dates into Zend_Date
                
                
                
                */
                
                
                
                if (in_array($key, array(
                    'from_date',
                    'to_date'
                )) && $value) {
                    
                    
                    
                    $value = Mage::app()->getLocale()->date($value, Varien_Date::DATE_INTERNAL_FORMAT, null, false);
                    
                    
                    
                }
                
                
                
                $this->setData($key, $value);
                
                
                
            }
            
            
            
        }
        
        
        
        return $arr;
        
        
        
    }
    
    
    
    
    
    
    
    /**
    
    
    
    * Returns rule as an array for admin interface
    
    
    
    *
    
    
    
    * Output example:
    
    
    
    * array(
    
    
    
    *   'name'=>'Example rule',
    
    
    
    *   'conditions'=>{condition_combine::asArray}
    
    
    
    *   'actions'=>{action_collection::asArray}
    
    
    
    * )
    
    
    
    *
    
    
    
    * @return array
    
    
    
    */
    
    
    
    public function asArray(array $arrAttributes = array())
    {
        
        
        
        $out = array(
            
            
            
            'name' => $this->getName(),
            
            
            
            'start_at' => $this->getStartAt(),
            
            
            
            'expire_at' => $this->getExpireAt(),
            
            
            
            'description' => $this->getDescription(),
            
            
            
            'conditions' => $this->getConditions()->asArray(),
            
            
            
            'actions' => $this->getActions()->asArray()
            
            
            
        );
        
        
        
        
        
        
        
        return $out;
        
        
        
    }
    
    
    
    
    
    
    
    public function validate(Varien_Object $object)
    {
        
        
        
        return $this->getConditions()->validate($object);
        
        
        
    }
    
    
    
    public function getResourceCollection()
    {
        
        
        
        return Mage::getResourceModel('followupemail/rules_collection');
        
        
        
    }
    
    
    
    public function afterLoad()
    {
        
        
        
        $this->_afterLoad();
        
        
        
    }
    
    
    
    
    
    
    
    protected function _afterLoad()
    {
        
        
        
        parent::_afterLoad();
        
        
        
        $conditionsArr = unserialize($this->getConditionsSerialized());
        
        
        
        if (!empty($conditionsArr) && is_array($conditionsArr)) {
            
            
            
            $this->getConditions()->loadArray($conditionsArr);
            
            
            
        }
        
        
        
        
        
        
        
        $actionsArr = unserialize($this->getActionsSerialized());
        
        
        
        if (!empty($actionsArr) && is_array($actionsArr)) {
            
            
            
            $this->getActions()->loadArray($actionsArr);
            
            
            
        }
        
        
        
        
        
        
        
        //        $websiteIds = $this->_getData('website_ids');
        
        
        
        //        if (is_string($websiteIds)) {
        
        
        
        //            $this->setWebsiteIds(explode(',', $websiteIds));
        
        
        
        //        }
        
        
        
        //        $groupIds = $this->getCustomerGroupIds();
        
        
        
        //        if (is_string($groupIds)) {
        
        
        
        //            $this->setCustomerGroupIds(explode(',', $groupIds));
        
        
        
        //        }
        
        
        
    }
    
    
    
    
    
    
    
    /**
    
    
    
    * Prepare data before saving
    
    
    
    *
    
    
    
    * @return Mage_Rule_Model_Rule 
    
    
    
    */
    
    
    
    protected function _beforeSave()
    {
        
        
        
        // check if discount amount > 0
        
        
        
        //        if ((int)$this->getDiscountAmount() < 0) {
        
        
        
        //            Mage::throwException(Mage::helper('rule')->__('Invalid discount amount.'));
        
        
        
        //        }
        
        
        
        
        
        
        
        
        
        
        
        if ($this->getConditions()) {
            
            
            
            $this->setConditionsSerialized(serialize($this->getConditions()->asArray()));
            
            
            
            $this->unsConditions();
            
            
            
        }
        
        
        
        if ($this->getActions()) {
            
            
            
            $this->setActionsSerialized(serialize($this->getActions()->asArray()));
            
            
            
            $this->unsActions();
            
            
            
        }
        
        
        
        
        
        
        
        //        $this->_prepareWebsiteIds();
        
        
        
        //
        
        
        
        //        if (is_array($this->getCustomerGroupIds())) {
        
        
        
        //            $this->setCustomerGroupIds(join(',', $this->getCustomerGroupIds()));
        
        
        
        //        }
        
        
        
        parent::_beforeSave();
        
        
        
    }
    
    
    
    
    
    
    
    /**
    
    
    
    * Combain website ids to string
    
    
    
    *
    
    
    
    * @return Mage_Rule_Model_Rule
    
    
    
    */
    
    
    
    //    protected function _prepareWebsiteIds()
    
    
    
    //    {
    
    
    
    //        if (is_array($this->getWebsiteIds())) {
    
    
    
    //            $this->setWebsiteIds(join(',', $this->getWebsiteIds()));
    
    
    
    //        }
    
    
    
    //        return $this;
    
    
    
    //    }
    
    
    
    
    
    
    
    /**
    
    
    
    * Check availabitlity to delete model
    
    
    
    *
    
    
    
    * @return boolean
    
    
    
    */
    
    
    
    public function isDeleteable()
    {
        
        
        
        return $this->_isDeleteable;
        
        
        
    }
    
    
    
    
    
    
    
    /**
    
    
    
    * Set is deleteable flag
    
    
    
    *
    
    
    
    * @param boolean $flag
    
    
    
    * @return Mage_Rule_Model_Rule
    
    
    
    */
    
    
    
    public function setIsDeleteable($flag)
    {
        
        
        
        $this->_isDeleteable = (bool) $flag;
        
        
        
        return $this;
        
        
        
    }
    
    
    
    
    
    
    
    
    
    
    
    /**
    
    
    
    * Checks model is readonly
    
    
    
    *
    
    
    
    * @return boolean
    
    
    
    */
    
    
    
    public function isReadonly()
    {
        
        
        
        return $this->_isReadonly;
        
        
        
    }
    
    
    
    
    
    
    
    /**
    
    
    
    * Set is readonly flag
    
    
    
    *
    
    
    
    * @param boolean $value
    
    
    
    * @return Mage_Rule_Model_Rule
    
    
    
    */
    
    
    
    public function setIsReadonly($value)
    {
        
        
        
        $this->_isReadonly = (boolean) $value;
        
        
        
        return $this;
        
        
        
    }
    
    //get template email
    
    
    
    public function getTemplate($templateId, $rule, $storeId = null)
    {
    
        if (is_null($storeId))
            $storeId = Mage::app()->getStore()->getId();
        
        
        
        $templateName = substr($templateId, false !== ($pos = strpos($templateId, MW_FollowUpEmail_Model_System_Config_Emailtemplate::TEMPLATE_SOURCE_SEPARATOR)) ? $pos + 1 : 0);
 
        if (!$pos) {
        
            return false;
          
        }

        else {
          
            switch ($src = substr($templateId, 0, $pos)) {
               
                case MW_FollowUpEmail_Model_System_Config_Emailtemplate::TEMPLATE_SOURCE_EMAIL:
                   
                    $template = $this->getResource()->getTemplateContent('core/email_template', $templateName);
                   
                    break;
                
                case MW_FollowUpEmail_Model_System_Config_Emailtemplate::TEMPLATE_SOURCE_NEWSLETTER:
                   
                    $template = $this->getResource()->getTemplateContent('newsletter/template', $templateName);
                    
                    break;
             
                default:
                 
                  
            }
           
            if (!$template) {
                
                return false;
               
            }
            
            $sender = Mage::getStoreConfig('followupemail/config/sender',$storeId);
            
            $template['sender_name'] = $rule['sender_name'] ? $rule['sender_name'] : Mage::getStoreConfig("trans_email/ident_$sender/name", $storeId);
            
            $template['sender_email'] = $rule['sender_email'] ? $rule['sender_email'] : Mage::getStoreConfig("trans_email/ident_$sender/email", $storeId);
            
            return $template;
           
        }
        
    }
    
    public function getAllEmailRulesFromResource($ruleId)
    {
        
        
        
        return $this->getResource()->getAllEmailRule($ruleId);
        
        
        
    }
    
    
    
    
    
    
    
    public function sendTestEmail($data)
    {
        
        
        
        $emailChain = $data['email_chain'];
        
        
        
        $cart = null;
        
        
        
        $orderId = "";
        
        
        
        $customerId = "";
        
        
        
        $storeId = Mage::app()->getStore()->getStoreId();
        
        
        
        $code     = "";
        $codeCart = "";
        
        
        
        $productIds = array();
        
        
        
        $senderInfo = array();
        
        
        
        $senderInfo['sender_email'] = $data['sender_email'];
        
        
        
        $senderInfo['sender_name'] = $data['sender_name'];
        
        
        
        $webId = Mage::app()->getWebsite()->getId();
        
        if ($webId == 0)
            $webId = 1;
        
        
        
        if ($data['testemail']['test_customer_name'] != "") {
            
            
            
            $customer = Mage::getModel("customer/customer");
            
            
            
            $customer->setWebsiteId($webId);
            
            
            
            $customer->loadByEmail($data['testemail']['test_customer_name']); //load customer by email id			
            
            
            
            if ($customer != null) {
                
                
                
                $customerId = $customer->getId();
                
                
                
                $resource = Mage::getSingleton('core/resource');
                
                
                
                $read = $resource->getConnection('core_read');
                
                
                
                $select = $read->select()->from(array(
                    'q' => $resource->getTableName('sales/quote')
                ), array(
                    
                    
                    
                    'store_id' => 'q.store_id',
                    
                    
                    
                    'quote_id' => 'q.entity_id',
                    
                    
                    
                    'customer_id' => 'q.customer_id',
                    
                    
                    
                    'subtotal' => 'q.subtotal',
                    
                    'subtotal_with_discount' => 'q.subtotal_with_discount',
                    'grand_total' => 'q.grand_total',
                    
                    
                    
                    'items_qty' => 'q.items_qty',
                    
                    
                    
                    //'store_id' => 'q.store_id',
                    
                    
                    
                    'updated_at' => 'q.updated_at'
                ))->joinLeft(array(
                    'a' => $resource->getTableName('sales/quote_address')
                ), 'q.entity_id=a.quote_id AND a.address_type="billing"', array(
                    
                    
                    
                    'customer_email' => new Zend_Db_Expr('IFNULL(q.customer_email, a.email)'),
                    
                    
                    
                    'customer_firstname' => new Zend_Db_Expr('IFNULL(q.customer_firstname, a.firstname)'),
                    
                    
                    
                    'customer_middlename' => new Zend_Db_Expr('IFNULL(q.customer_middlename, a.middlename)'),
                    
                    
                    
                    'customer_lastname' => new Zend_Db_Expr('IFNULL(q.customer_lastname, a.lastname)')
                    
                    
                    
                ))->joinInner(array(
                    'i' => $resource->getTableName('sales/quote_item')
                ), 'q.entity_id=i.quote_id', array(
                    
                    
                    
                    'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.product_id)'),
                    
                    
                    
                    'item_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.item_id)')
                    
                    
                    
                ))->where('q.is_active=1')->where('q.customer_email = ?', $data['testemail']['test_customer_name']) /*->where('q.updated_at < ?', date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,
                
                
                
                $now - $intTimeLastHour))*/ ->where('q.items_count>0')->where('i.parent_item_id IS NULL')->group('q.entity_id')->order('updated_at');
                
                
                
                //mage::log(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,$now - ($intFromTimeHour+$intTimeLastHour)));
                
                
                
                $carts = $read->fetchAll($select);
                
                
                
                
                
                foreach ($carts as $_cart) {
                    
                    
                    
                    $cart = $_cart;
                    
                    
                    
                }
                
                
                
            }
            
            
            
        }
        
        if ($cart != null)
            $codeCart = MW_FollowUpEmail_Helper_Data::encryptCode($data['testemail']['test_customer_name'], 'cart', 0);
        
        if ($data['testemail']['test_order_id'] != "") {
            
            
            
            $_order = Mage::getModel('sales/order')->loadByIncrementId($data['testemail']['test_order_id']);
            
            
            
            $orderId = $_order->getId();
            
            $code = MW_FollowUpEmail_Helper_Data::encryptCode($data['testemail']['test_customer_name'], 'order', $orderId);
            
            $items = $_order->getAllItems();
            
            
            
            foreach ($items as $item) {
                if ($item->getParentItem())
                    continue;
                $productIds[] = $item->getProductId();
                
                
                
            }
            
            
            
        }
        
        
        
        $error = true;
        
        
        
        $templateId = "";
        
        
        
        foreach ($emailChain as $emailChainItem) {
            
            
            
            $params = array();
            
            
            
            if ($templateId == $emailChainItem['TEMPLATE_ID'])
                continue;
            
            
            
            $templateId = $emailChainItem['TEMPLATE_ID'];
            
            
            
            //get content of current email template							
            
            
            
            $emailTemplate = $this->getTemplate($emailChainItem['TEMPLATE_ID'], $senderInfo);
            
            
            
            
            $timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];
            
            
            
            //$code = MW_FollowUpEmail_Helper_Data::getCodeSecurity();			
            
            
            
            $params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];
            
            
            
            $params['senderInfo'] = $senderInfo;
            
            
            
            $params['productIds'] = $productIds;
            
            
            
            $params['orderId'] = $orderId;
            
            
            
            $params['data'] = "";
            
            
            
            $params['customer'] = "";
            
            
            
            $params['customerId'] = $customerId;
            
            
            
            $params['cart'] = $cart;
            
            
            
            $params['storeId'] = $storeId;
            
            
            
            $params['code']     = $code;
            $params['codeCart'] = $codeCart;
            
            
            
            if (!$this->send($params, $emailTemplate, $data['testemail']['test_recipient']))
                $error = false;
            
            
            
        }
        
        
        
        return $error;
        
        
        
    }
    
    
    
    
    
    
    
    /*
    
    
    
    * Sends test email
    
    
    
    * @return bool|Exception Sending result
    
    
    
    */
    
    
    
    protected function send($params, $emailTemplate, $recipient)
    {
        
        
        
        $email = Mage::getModel('core/email_template');
        
        
        
        $translate = Mage::getSingleton('core/translate');
        
        
        
        /* @var $translate Mage_Core_Model_Translate */
        
        
        
        $translate->setTranslateInline(false);
        
        
        
        // email content
        
        
        $content = Mage::helper('followupemail')->_prepareContentEmail($params);
        
        $_subject = Mage::helper('followupemail')->_prepareSubjectEmail($params, $emailTemplate['subject']);
        
        $subject = htmlspecialchars("[Test] " . $_subject);
        
        
        
        $message = nl2br(htmlspecialchars($content));
        
        
        
        $sender = array(
            
            
            
            'name' => strip_tags($emailTemplate['sender_name']),
            
            
            
            'email' => strip_tags($emailTemplate['sender_email'])
            
            
            
        );
        
        
        
        $name = array(
            
            
            
            'name' => $recipient,
            
            
            
            'email' => $recipient
        );
        
        
        
        $email->setReplyTo($sender['email']);
        
        
        
        $email->setSenderName($sender['name']);
        
        
        
        $email->setSenderEmail($sender['email']);
        
        
        
        $email->setTemplateSubject($subject);
        
        
        
        $email->setTemplateText($content);
        
        
        
        $email->setDesignConfig(array(
            'area' => 'frontend',
            'store' => $params['storeId']
        ));
        
        
        
        $recipients = array(
            $name['email']
        );
        
        
        
        $result = $email->send($recipients, null, array(
            
            
            
            'name' => $name['name'],
            
            
            
            'email' => $name['email'],
            
            
            
            'subject' => $subject,
            
            
            
            'message' => $message
            
            
            
        ));
        
        
        
        $translate->setTranslateInline(true);
        
        
        
        return $result;
        
        
        
    }
    
    
    
    
    
    
    
}