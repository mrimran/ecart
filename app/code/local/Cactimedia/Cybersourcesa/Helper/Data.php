<?php
/**
 * @category   Cactimedia
 * @package    Cactimedia_Cybersourcesa
 * @author     magepsycho@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Cactimedia_Cybersourcesa_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfig($field, $default = null)
	{
        $value = Mage::getStoreConfig('payment/cybersourcesa/' . $field);
        if(!isset($value) or trim($value) == ''){
            return $default;
        }else{
            return $value;
        }
    }

    public function log($data)
	{
		if(!$this->getConfig('enable_log')){
			return;
		}
		$separator = "===================================================================";
        Mage::log($separator, null, 'cybersourcesa.log', true);
        Mage::log($data, null, 'cybersourcesa.log', true);
    }

	public function getHashSign($params, $signedField = 'signed_field_names')
	{
		$signedFieldNames = explode(",", $params[$signedField]);
        foreach ($signedFieldNames as &$field) {
           $dataToSign[] = $field . "=" . $params[$field];
        }
        $data      =  implode(",", $dataToSign);
		$secretKey = $this->getConfig('secret_key');
		$hashSign = base64_encode(hash_hmac('sha256', $data, $secretKey, true));
		return $hashSign;
	}
}