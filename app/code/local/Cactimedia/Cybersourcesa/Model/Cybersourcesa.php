<?php
/**
 * @category   Cactimedia
 * @package    Cactimedia_Cybersourcesa
 * @author     magepsycho@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Cactimedia_Cybersourcesa_Model_Cybersourcesa extends Mage_Payment_Model_Method_Abstract
{
    const PAYMENT_LIVE_URL       = 'https://secureacceptance.cybersource.com/pay';
    const PAYMENT_TEST_URL       = 'https://testsecureacceptance.cybersource.com/pay';

    protected $_code 			= 'cybersourcesa';
    protected $_formBlockType 	= 'cybersourcesa/form';
    protected $_infoBlockType 	= 'cybersourcesa/info';

	protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

	//protected $_allowCurrencyCode = array('EUR', 'USD');

	public function validate()
    {
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currencyCode = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currencyCode = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
        if (!$this->canUseForCurrency($currencyCode)) {
            Mage::throwException(Mage::helper('cybersourcesa')->__('Selected currency code ('.$currencyCode.') is not compatabile with this payment.'));
        }
        return $this;
    }

	public function canUseForCurrency($currencyCode)
    {
//        if (!in_array($currencyCode, $this->_allowCurrencyCode)) {
//            return false;
//        }
        return true;
    }

    public function canCapture()
    {
        return true;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            	->setLastTransId($this->getTransactionId());

        return $this;
    }

	public function getIssuerUrls()
	{
		return array("live" => self::PAYMENT_LIVE_URL,
					 "test" => self::PAYMENT_TEST_URL);

	}

	public function getCybersourcesaUrl()
	{
		$setIssuerUrls 	= $this->getIssuerUrls();
		if($this->getConfigData('mode')){
			return $setIssuerUrls["live"];
		}else{
			return $setIssuerUrls["test"];
		}
	}

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('cybersourcesa/process/redirect');
    }

    protected function getSuccessUrl()
	{
		return Mage::getUrl('cybersourcesa/process/success', array('_secure' => true));
	}

	protected function getFailureUrl()
    {
        return Mage::getUrl('cybersourcesa/process/failure', array('_secure' => true));
    }

    protected function getCancelUrl()
    {
        return Mage::getUrl('cybersourcesa/process/cancel', array('_secure' => true));
    }

    protected function getIpnUrl()
    {
        return Mage::getUrl('cybersourcesa/process/ipn', array('_secure' => true));
    }

	public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        return $this->_customer;
    }

    public function getCheckout()
    {
        if (empty($this->_checkout)) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }

    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }

    public function getOrder()
    {
        if (empty($this->_order)) {
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
            $this->_order = $order;
        }
        return $this->_order;
    }

	public function getEmail()
	{
		$email = $this->getOrder()->getCustomerEmail();
		if (!$email) {
            $email = $this->getQuote()->getBillingAddress()->getEmail();
        }
		if (!$email) {
            $email = Mage::getStoreConfig('trans_email/ident_general/email');
        }
		return $email;
	}

	public function getOrderAmount()
	{
    	$amount = sprintf('%.2f', $this->getOrder()->getGrandTotal());
    	return $amount;
	}

	public function getOrderCurrency()
	{
		$currency = $this->getOrder()->getOrderCurrency();
        if (is_object($currency)) {
            $currency = $currency->getCurrencyCode();
        }
		return $currency;
		#return Mage::app()->getStore()->getCurrentCurrencyCode();
	}

	public function getHashSign($formFields)
	{
		$hashSign = Mage::helper('cybersourcesa')->getHashSign($formFields);
		return $hashSign;
	}

	public function getFormFields()
	{
		$payment		= $this->getQuote()->getPayment();
		$order			= $this->getOrder();
		$formFields	    = array();

		//prepare variables for hidden form fields
		$formFields['access_key']			 = $this->getConfigData('access_key'); //'22b36766dde234e38adada8b3a6c7314';
		$formFields['profile_id']			 = $this->getConfigData('profile_id'); //'LABISNI';
		$formFields['transaction_uuid']		 = Mage::helper('core')->uniqHash();
		$formFields['signed_field_names']	 = 'access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type,reference_number,amount,currency,bill_to_address_city,bill_to_address_country,bill_to_address_line1,bill_to_address_line2,bill_to_address_postal_code,bill_to_address_state,bill_to_company_name,bill_to_email,bill_to_forename,bill_to_surname,bill_to_phone,customer_ip_address';

		$formFields['signed_field_names']	 .= ',merchant_defined_data1,merchant_defined_data2,merchant_defined_data3,merchant_defined_data5,merchant_defined_data6,merchant_defined_data7,merchant_defined_data8,merchant_defined_data9,merchant_defined_data10,merchant_defined_data11,merchant_defined_data12,merchant_defined_data13,merchant_defined_data14,merchant_defined_data18,merchant_defined_data19,merchant_defined_data21,merchant_defined_data25';

		$formFields['unsigned_field_names']	 = '';
		$formFields['signed_date_time']		 = gmdate("Y-m-d\TH:i:s\Z", time() + 63*60);
		$formFields['locale']				 = 'en';
		$formFields['transaction_type']		 = 'sale';
		$formFields['reference_number']		 = $order->getRealOrderId();
		$formFields['amount']				 = $this->getOrderAmount();
		$formFields['currency']				 = $this->getOrderCurrency();

		$billingAddress = $order->getBillingAddress();
		$formFields['bill_to_address_city']			 = $billingAddress->getCity();
		$formFields['bill_to_address_country']		 = $billingAddress->getCountry();
		$formFields['bill_to_address_line1']		 = $billingAddress->getStreet(1);
		$formFields['bill_to_address_line2']		 = $billingAddress->getStreet(2);
		$formFields['bill_to_address_postal_code']	 = $billingAddress->getPostcode();
		$formFields['bill_to_address_state']		 = $billingAddress->getRegion();
		$formFields['bill_to_company_name']			 = $billingAddress->getCompany();

		$formFields['bill_to_email']				= $this->getEmail();
		$formFields['bill_to_forename']				= $billingAddress->getFirstname();
		$formFields['bill_to_surname']				= $billingAddress->getLastname();
		$formFields['bill_to_phone']				= $billingAddress->getTelephone();
		$formFields['customer_ip_address']			= Mage::helper('core/http')->getRemoteAddr();


		$items = $order->getAllItems();
		$categories = array();
		$products = array();
		foreach($items as $_item){
			$productId = $_item->getProductId();
			$product	 = Mage::getModel('catalog/product')->load($productId);
			$cats		= $product->getCategoryIds();
			foreach ($cats as $category_id) {
				$_cat = Mage::getModel('catalog/category')->load($category_id) ;
				$categories[] = $_cat->getName();
			}
			$products[] = $_item->getName();
		}

		$formFields['merchant_defined_data1']				= '10'; //Number of Failed Authorizations Attempts
		$formFields['merchant_defined_data2']				= '10'; //Number of orders to date since registering
		$formFields['merchant_defined_data3']				= 'Web'; //Sales channel
		$formFields['merchant_defined_data5']				= date('d-m-Y h:i'); //last password change
		$formFields['merchant_defined_data6']				= date('d-m-Y h:i'); //last email change
		$formFields['merchant_defined_data7']				= 'NO'; //Guest account
		$formFields['merchant_defined_data8']				= implode(',', array_unique($categories)); //Product Category
		$formFields['merchant_defined_data9']				= implode(',', array_unique($products)); //
		$formFields['merchant_defined_data10']				= $order->getShippingDescription(); //Shipping Method
		$formFields['merchant_defined_data11']				= 'Home'; //Delivery Type
		$formFields['merchant_defined_data12']				= 'NO'; //previous customer
		$formFields['merchant_defined_data13']				= '100'; //Account Age
		$formFields['merchant_defined_data14']				= date('d-m-Y h:i',(strtotime ( '-1 day' ) )); //Time since last purchase
		$formFields['merchant_defined_data18']				= '1'; //Number of password change
		$formFields['merchant_defined_data19']				= '0'; //Number of email change
		$formFields['merchant_defined_data21']				= count($items); //Number of items sold in the order
		$formFields['merchant_defined_data25']				= $order->getShippingAddress()->getCountry(); //Product Shipping Country Name

		$formFields['signature']					= $this->getHashSign($formFields);

		//Log request info
        if($this->getConfigData('debug_flag')){
            Mage::helper('cybersourcesa')->log($formFields);//for debug purpose
            $resource       = Mage::getSingleton('core/resource');
            $connection 	= $resource->getConnection('core_write');
    	    $sql            = "INSERT INTO ".$resource->getTableName('cybersourcesa_api_debug')." SET created_time = ?, request_body = ?, response_body = ?";
    	    $connection->query($sql, array(date('Y-m-d H:i:s'), $this->getCybersourcesaUrl()."\n".print_r($formFields, 1), ''));
        }

		return $formFields;
	}
}