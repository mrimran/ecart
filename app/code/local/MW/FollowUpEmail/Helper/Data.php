<?php



class MW_FollowUpEmail_Helper_Data extends Mage_Core_Helper_Abstract

{

    public static function getOrderAddress($order, $addressType)

    {

        $addresses = Mage::getResourceModel('sales/order_address_collection')

            ->addAttributeToSelect('*')

            ->addAttributeToFilter('parent_id', $order->getId());

        if ($order->getId()) foreach ($addresses as $address) $address->setOrder($order);

        foreach ($addresses as $address)

            if ($addressType == $address->getAddressType() && !$address->isDeleted())

                return $address;



        return false;

    }



    public function renderItemsOrder($order){

        $layout = Mage::getSingleton('core/layout');

        $block = $layout->createBlock('followupemail/itemsorder');

        $block->setOrder($order);

        return $block->renderView();

    }



    public function renderItemsproductorder($order){

        $layout = Mage::getSingleton('core/layout');

        //$dungdk   = $bc->createBlock('sales/order_items')->append($freeGift, 'freegiftbox');            

        $block = $layout->createBlock('followupemail/itemsproductorder');

        $block->setOrder($order);

        return $block->renderView();
        //Mage::log(get_class($block));

        /*$block = $bc->createBlock('sales/order_items')->assign('order', $order)->addItemRender('simple', 'sales/order_item_renderer_default', 'sales/order/items/renderer/default.phtml')->setTemplate('sales/order/items.phtml'); */

        //Mage::log($block->renderHtml();

    }



    public function renderProductNames($products){

        $count = count($products);

        $i = 0;

        $html = "";

        if(is_array($products)){

            foreach($products as $product){

                $i++;

                $obj = Mage::getModel('catalog/product');

                $_product = $obj->load($product);

                //$url = $_product->getProductUrl();    

                $url = Mage::getUrl($_product->getUrlPath());

                if($i == $count-1){

                    $html .= '<a href="'.$url.'">'.$_product->getName().'</a>'." and ";

                }

                else if($i == $count){

                    $html .= '<a href="'.$url.'">'.$_product->getName().'</a>';

                }

                else{

                    $html .= '<a href="'.$url.'">'.$_product->getName().'</a>'." , ";

                }

                //$html .= '<a href="'.$url.'">'.$_product->getName().'</a>';                

            }

        }

        return $html;

    }



    public function renderProductReviews($products){

        $count = count($products);

        $i = 0;

        $html = "";

        if(is_array($products)){

            foreach($products as $product){

                $i++;

                $obj = Mage::getModel('catalog/product');

                $_product = $obj->load($product);

                $urlProduct = Mage::getUrl($_product->getUrlPath());

                $url = $_product->getConnectUrl();

                if($i == $count-1){

                    if($url == "")

                        $html .= '<a href="'.$urlProduct.'">'.$_product->getName().'</a>'." and ";

                    else

                        $html .= '<a href="'.$url.'">'.$_product->getName().'</a>'." and ";

                }

                else if($i == $count){

                    if($url == "")

                        $html .= '<a href="'.$urlProduct.'">'.$_product->getName().'</a>';

                    else

                        $html .= '<a href="'.$url.'">'.$_product->getName().'</a>';

                }

                else{

                    if($url == "")

                        $html .= '<a href="'.$urlProduct.'">'.$_product->getName().'</a>'." , ";

                    else

                        $html .= '<a href="'.$url.'">'.$_product->getName().'</a>'." , ";

                }

                //$html .= '<a href="'.$url.'">'.$_product->getName().'</a>';                

            }

        }

        return $html;

    }



    public function renderItemsproductcart($items){

        $bc = Mage::getSingleton('core/layout');

        //$dungdk   = $bc->createBlock('sales/order_items')->append($freeGift, 'freegiftbox');            

        $block = $bc->createBlock('followupemail/itemsproductcart');

        $block->setItems($items);

        return $block->renderView();

    }



    public function renderItemscart($items,$cartSubtotal,$cartsubtotal_with_discount,$cartgrand_total){

        $bc = Mage::getSingleton('core/layout');

        $discount = 0;

        if(($cartSubtotal - $cartsubtotal_with_discount) > 0) $discount = $cartSubtotal - $cartsubtotal_with_discount;

        $block = $bc->createBlock('followupemail/itemscart');

        $block->setItems($items);

        $block->setSubtotal($cartSubtotal);

        $block->setDiscount($discount);

        $block->setGrandTotal($cartgrand_total);

        return $block->renderView();

    }



    public static function explodeEmailList($emails)

    {

        if (!$emails) return array();

        $emails = trim(str_replace(array(',', ';'), ' ', $emails));

        do {

            $emails = str_replace('  ', ' ', $emails);

        } while (strpos($emails, '  ') !== false);

        $result = explode(' ', $emails);

        return $result;

    }



    public static function encryptCode($email,$page,$orderId)

    {

        $code = $email.",".$page.",".$orderId;

        return Mage::helper('core')->encrypt($code);

    }



    public static function decryptCode($code)

    {

        return Mage::helper('core')->decrypt($code);

    }



    public static function getCodeSecurity()

    {

        return md5(mt_rand());

    }

    public function getCustomerByEmail($email){
        $webId = Mage::app()->getWebsite()->getId();
        if($webId == 0) $webId = 1;
        $customer = Mage::getModel("customer/customer");
        $customer->setWebsiteId($webId);
        $customer->loadByEmail($email); //load customer by email id    ;
        return $customer;
    }

    public function _prepareSubjectEmail($params,$_subject){
        $subject = $_subject;
        $store = Mage::getModel('core/store')->load($params['storeId']);
        $subject = str_replace("{{var store.getFrontendName()}}", $store->getFrontendName(), $subject);
        return $subject;
    }

    public function _prepareContentEmail($params,$queueId="",$preview = false){

        $emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($params['templateEmailId'],$params['senderInfo']);
        $content = $emailTemplate['content'];
        $store = Mage::getModel('core/store')->load($params['storeId']);

        $content = str_replace("{{var store.getFrontendName()}}", $store->getFrontendName(), $content);

        $content = str_replace("{{var coupon.code}}", $params['coupon'], $content);

        $customer_email = $params['customer']['customer_email'];
        $base_email = base64_encode($customer_email);


        $success_url = Mage::getUrl('followupemail/index/success').'code/'.$base_email;
        $content = str_replace("{{ var fue.unsubscribe()}}",$success_url,$content);

        $directLink = "";
        $directLinkCart = "";
        $arrCode = explode(',',$this->decryptCode($params['code']));
        if(is_array($arrCode)){
            //$customer = $this->getCustomerByEmail($arrCode[0]);
            //if($customer->getId()){            
            if($arrCode[1] == 'order'){
                $directLink = $store->getUrl('followupemail/index/direct', array('code' => str_replace('/','special',$params['code'])));
            }
            else if($arrCode[1] == 'cart'){
                $directLinkCart = $store->getUrl('followupemail/index/direct', array('code' => str_replace('/','special',$params['code'])));
            }
            else{
                $directLink = "";
                $directLinkCart = "";
            }
            //}
//            else{
//                $directLink = "";
//                $directLinkCart = "";
//            }

        }
        if(!isset($arrCode[1])){
            $directLink = $store->getUrl('followupemail/index/direct', array('code' => $params['code']));
            $directLinkCart = $store->getUrl('followupemail/index/direct', array('code' => $params['code']));
        }
        if(isset($params['codeCart'])){
            $arrCodeCart = explode(',',$this->decryptCode($params['codeCart']));
            if(is_array($arrCodeCart)){
                //$customer = $this->getCustomerByEmail($arrCodeCart[0]);
                //if($customer->getId()){                    
                if($arrCodeCart[1] == 'cart'){
                    $directLinkCart = $store->getUrl('followupemail/index/direct', array('code' => str_replace('/','special',$params['codeCart'])));
                }
                else{
                    $directLinkCart = "";
                }
                //}
//                else{                    
//                    $directLinkCart = "";
//                }

            }
        }

        $productNamesOrder = "";

        $productReviewsOrder = "";

        $productNamesCart = "";

        $productReviewsCart = "";

        if($params['productIds'] != null && $params['orderId'] != ""){

            $productReviewsOrder = $this->renderProductReviews($params['productIds']);

            $productNamesOrder = $this->renderProductNames($params['productIds']);

        }
        //Variables Order

        $order_id = $params['orderId'];

        $itemsOrder= "";

        $itemsProductOrder = "";

        $orderCreateAtDate = "";

        $orderBillingAddress = "";

        $orderShippingAddress = "";

        $orderShippingMethod = "";

        $orderPaymentMethod = "";

        $orderStatus = "";

        $orderSubtotal = "";

        $orderNumber = "";

        $orderQty = "";

        /*{{layout handle="sales_email_order_items" order=$order}}*/
        $customerInfo = array();
        if($order_id != ""){

            $order = Mage::getModel('sales/order')->load($order_id);
            $customerInfo = Mage::getModel('followupemail/observer')->_getCustomer($params['customerId'],$order);
            if($order->getData() != null){

                $itemsOrder = $this->renderItemsOrder($order);

                $itemsProductOrder = $this->renderItemsproductorder($order);
                $payment = $order->getPayment();

                $orderCreateAtDate = $order->getCreatedAtDate();

                $orderBillingAddress = $order->getBillingAddress()->format('html');

                $orderShippingAddress = $order->getShippingAddress()->format('html');

                $orderShippingMethod = $order->getShippingDescription();

                if($payment != null)

                    $orderPaymentMethod = $payment->getMethodInstance()->getTitle();



                $orderStatus = $order->getStatus();

                $orderSubtotal = $order->getGrandTotal();

                $orderNumber = $order->getIncrementId();

                $orderQty = $order->getTotalQtyOrdered();
            }

        }

        if($itemsProductOrder != null){
            $content = $this->replace_depend("depend","order.products",$content);
            if(strpos($content, "{{var order.products}}")){
                $content = str_replace("{{var order.products}}", $itemsProductOrder, $content);
            }
        }
        else{
            $content = $this->remove_depend("depend","order.products",$content);
        }



        if($orderCreateAtDate != null){
            $content = $this->replace_depend("depend","order.createAt",$content);
            if(strpos($content, "{{var order.createAt}}"))
                $content = str_replace("{{var order.createAt}}", $orderCreateAtDate, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.createAt",$content);
        }



        if($orderBillingAddress != null){
            $content = $this->replace_depend("depend","order.billing_address",$content);
            if(strpos($content, "{{var order.billing_address}}"))
                $content = str_replace("{{var order.billing_address}}", $orderBillingAddress, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.billing_address",$content);
        }


        if($orderShippingAddress != null){
            $content = $this->replace_depend("depend","order.shipping_address",$content);
            if(strpos($content, "{{var order.shipping_address}}"))
                $content = str_replace("{{var order.shipping_address}}", $orderShippingAddress, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.shipping_address",$content);
        }


        if($orderShippingMethod != null){
            $content = $this->replace_depend("depend","order.shipping_method",$content);
            if(strpos($content, "{{var order.shipping_method}}"))
                $content = str_replace("{{var order.shipping_method}}", $orderShippingMethod, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.shipping_method",$content);
        }


        if($directLink != null){
            $content = $this->replace_depend("depend","order.direct_link",$content);
            if(strpos($content, "{{var order.direct_link}}"))
                $content = str_replace("{{var order.direct_link}}", $directLink, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.direct_link",$content);
        }


        if($orderPaymentMethod != null){
            $content = $this->replace_depend("depend","order.payment_method",$content);
            if(strpos($content, "{{var order.payment_method}}"))
                $content = str_replace("{{var order.payment_method}}", $orderPaymentMethod, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.payment_method",$content);
        }


        if($orderStatus != null){
            $content = $this->replace_depend("depend","order.status",$content);
            if(strpos($content, "{{var order.status}}"))
                $content = str_replace("{{var order.status}}", $orderStatus, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.status",$content);
        }


        if($orderSubtotal != null){
            $content = $this->replace_depend("depend","order.subtotal",$content);
            if(strpos($content, "{{var order.subtotal}}"))
                $content = str_replace("{{var order.subtotal}}", $orderSubtotal, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.subtotal",$content);
        }


        if($orderNumber != null){
            $content = $this->replace_depend("depend","order.order_number",$content);
            if(strpos($content, "{{var order.order_number}}"))
                $content = str_replace("{{var order.order_number}}", $orderNumber, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.order_number",$content);
        }


        if($orderQty != null){
            $content = $this->replace_depend("depend","order.total_qty",$content);
            if(strpos($content, "{{var order.total_qty}}"))
                $content = str_replace("{{var order.total_qty}}", $orderQty, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.total_qty",$content);
        }


        if($productNamesOrder != null){
            $content = $this->replace_depend("depend","order.product_names",$content);
            if(strpos($content, "{{var order.product_names}}"))
                $content = str_replace("{{var order.product_names}}", $productNamesOrder, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.product_names",$content);
        }


        if($productReviewsOrder != null){
            $content = $this->replace_depend("depend","order.product_reviews",$content);
            if(strpos($content, "{{var order.product_reviews}}"))
                $content = str_replace("{{var order.product_reviews}}", $productReviewsOrder, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.product_reviews",$content);
        }


        if($itemsOrder != null){
            $content = $this->replace_depend("depend","order.items",$content);
            if(strpos($content, "{{var order.items}}"))
                $content = str_replace("{{var order.items}}", $itemsOrder, $content);
        }
        else{
            $content = $this->remove_depend("depend","order.items",$content);
        }

        // Variables Cart

        $cart = $params['cart'];

        $itemProductCart = "";

        $cartUpdateAt = "";

        $cartSubtotal = "";

        $cartItemQty = "";

        $itemscart = ""    ;

        if($cart != ""){

            $items = explode(',', $cart['item_ids']);

            $itemProductCart = $this->renderItemsproductcart($items);

            $cartUpdateAt = $cart['updated_at'];

            $productIds = explode(',', $cart['product_ids']);

            $productReviewsCart = $this->renderProductReviews($productIds);

            $productNamesCart = $this->renderProductNames($productIds);

            $cartSubtotal = $cart['subtotal'];

            $cartsubtotal_with_discount = $cart['subtotal_with_discount'];

            $cartgrand_total = $cart['grand_total'];

            $itemscart = $this->renderItemscart($items,$cartSubtotal,$cartsubtotal_with_discount,$cartgrand_total);

            $cartItemQty = $cart['items_qty'];

        }

        if($itemProductCart != null){
            $content = $this->replace_depend("depend","cart.products",$content);
            if(strpos($content, "{{var cart.products}}"))
                $content = str_replace("{{var cart.products}}", $itemProductCart, $content);
        }
        else{
            $content = $this->remove_depend("depend","cart.products",$content);
        }


        if($cartUpdateAt != null){
            $content = $this->replace_depend("depend","cart.update_at",$content);
            if(strpos($content, "{{var cart.update_at}}"))
                $content = str_replace("{{var cart.update_at}}", $cartUpdateAt, $content);
        }
        else{
            $content = $this->remove_depend("depend","cart.update_at",$content);
        }


        if($cartSubtotal != null){
            $content = $this->replace_depend("depend","cart.subtotal",$content);
            if(strpos($content, "{{var cart.subtotal}}"))
                $content = str_replace("{{var cart.subtotal}}", $cartSubtotal, $content);
        }
        else{
            $content = $this->remove_depend("depend","cart.subtotal",$content);
        }


        if($cartItemQty != null){
            $content = $this->replace_depend("depend","cart.items_qty",$content);
            if(strpos($content, "{{var cart.items_qty}}"))
                $content = str_replace("{{var cart.items_qty}}", $cartItemQty, $content);
        }
        else{
            $content = $this->remove_depend("depend","cart.items_qty",$content);
        }


        if($directLinkCart != null){
            $content = $this->replace_depend("depend","cart.direct_link",$content);
            if(strpos($content, "{{var cart.direct_link}}"))
                $content = str_replace("{{var cart.direct_link}}", $directLinkCart, $content);
        }
        else{
            $content = $this->remove_depend("depend","cart.direct_link",$content);
        }


        if($productNamesCart != null){
            $content = $this->replace_depend("depend","cart.product_names",$content);
            if(strpos($content, "{{var cart.product_names}}"))
                $content = str_replace("{{var cart.product_names}}", $productNamesCart, $content);
        }
        else{
            $content = $this->remove_depend("depend","cart.product_names",$content);
        }


        if($productReviewsCart != null){
            $content = $this->replace_depend("depend","cart.product_reviews",$content);
            if(strpos($content, "{{var cart.product_reviews}}"))
                $content = str_replace("{{var cart.product_reviews}}", $productReviewsCart, $content);
        }
        else{
            $content = $this->remove_depend("depend","cart.product_reviews",$content);
        }


        if($itemscart != null){
            $content = $this->replace_depend("depend","cart.items",$content);
            if(strpos($content, "{{var cart.items}}"))
                $content = str_replace("{{var cart.items}}", $itemscart, $content);
        }
        else{
            $content = $this->remove_depend("depend","cart.items",$content);
        }



        // Variables Customer



        $cData = $params['data'];

        $cFullName = "";

        $cEmail = "";

        $cLastName = "";

        $cFirstName = "";

        $cDefaultAddress = "";

        $cCity = "";

        $cState = "";

        $cZipCode = "";

        $cCountry = "";

        if($params['customerId'] != ""){

            $customerInfo = Mage::getModel('followupemail/observer')->_getCustomer($params['customerId'],null);
        }
        if($customerInfo != null){

            $cFullName = $customerInfo['customer_name'];

            $cEmail = $customerInfo['customer_email'];

            $cLastName = $customerInfo['last_name'];

            $cFirstName = $customerInfo['first_name'];

            $cDefaultAddress = $customerInfo['default_address'];

            $cCity = $customerInfo['city'];

            $cState = $customerInfo['state'];

            $cZipCode = $customerInfo['zip_code'];

            $cCountry = $customerInfo['country'];

        }

        if(isset($params['cart']) && $customerInfo == null){

            $cEmail = $params['cart']['customer_email'];

            $cLastName = $params['cart']['customer_lastname'];

            $cFirstName = $params['cart']['customer_firstname'];

            $cFullName =  $cFirstName . ' ' . ($params['cart']['customer_middlename'] ? $params['cart']['customer_middlename'] . ' ' : '') . $cLastName;

            $cCity = $params['cart']['city'];

            $cState = $params['cart']['state'];

            $cZipCode = $params['cart']['zipcode'];

            $countryName = Mage::getModel('directory/country')->load($params['cart']['country_id'])->getName();

            $cCountry = $countryName;
        }
        $posFullName = strpos(strtolower($cFullName), "n/a");
        if ($posFullName !== false) {
            $cFullName = "";
        }

        if($cFullName != null){
            $content = $this->replace_depend("depend","customer.full_name",$content);
            if(strpos($content, "{{var customer.full_name}}"))
                $content = str_replace("{{var customer.full_name}}", $cFullName, $content);

            $content = $this->replace_depend("depend","customer.name",$content);
            if(strpos($content, "{{var customer.name}}"))
                $content = str_replace("{{var customer.name}}", $cFullName, $content);
        }
        else{
            $content = $this->remove_depend("depend","customer.full_name",$content);
            $content = $this->remove_depend("depend","customer.name",$content);
        }


        /*if(strpos($content, "{{var customer.name}}"))
        $content = str_replace("{{var customer.name}}", $cFullName, $content);*/

        if($cEmail != null){
            $content = $this->replace_depend("depend","customer.email",$content);
            if(strpos($content, "{{var customer.email}}"))
                $content = str_replace("{{var customer.email}}", $cEmail, $content);
        }
        else{
            $content = $this->remove_depend("depend","customer.email",$content);
        }

        $posLastName = strpos(strtolower($cLastName), "n/a");
        if ($posLastName !== false) {
            $cLastName = "";
        }
        if($cLastName != null){
            $content = $this->replace_depend("depend","customer.last_name",$content);
            if(strpos($content, "{{var customer.last_name}}"))
                $content = str_replace("{{var customer.last_name}}", $cLastName, $content);
        }
        else{
            $content = $this->remove_depend("depend","customer.last_name",$content);
        }

        $posFirstName = strpos(strtolower($cFirstName), "n/a");
        if ($posFirstName !== false) {
            $cFirstName = "";
        }
        if($cFirstName != null){
            $content = $this->replace_depend("depend","customer.first_name",$content);
            if(strpos($content, "{{var customer.first_name}}"))
                $content = str_replace("{{var customer.first_name}}", $cFirstName, $content);
        }
        else{
            $content = $this->remove_depend("depend","customer.first_name",$content);
        }


        if($cDefaultAddress != null){
            $content = $this->replace_depend("depend","customer.default_address",$content);
            if(strpos($content, "{{var customer.default_address}}"))
                $content = str_replace("{{var customer.default_address}}", $cDefaultAddress, $content);
        }
        else{
            $content = $this->remove_depend("depend","customer.default_address",$content);
        }

        if($cCity != null){
            $content = $this->replace_depend("depend","customer.city",$content);
            if(strpos($content, "{{var customer.city}}"))
                $content = str_replace("{{var customer.city}}", $cCity, $content);
        }
        else{
            $content = $this->remove_depend("depend","customer.city",$content);
        }

        if($cState != null){
            $content = $this->replace_depend("depend","customer.state",$content);
            if(strpos($content, "{{var customer.state}}"))
                $content = str_replace("{{var customer.state}}", $cState, $content);
        }
        else{
            $content = $this->remove_depend("depend","customer.state",$content);
        }

        if($cZipCode != null){
            $content = $this->replace_depend("depend","customer.zip_code",$content);
            if(strpos($content, "{{var customer.zip_code}}"))
                $content = str_replace("{{var customer.zip_code}}", $cZipCode, $content);
        }
        else{
            $content = $this->remove_depend("depend","customer.zip_code",$content);
        }

        if($cCountry != null){
            $content = $this->replace_depend("depend","customer.country",$content);
            if(strpos($content, "{{var customer.country}}"))
                $content = str_replace("{{var customer.country}}", $cCountry, $content);
        }
        else{
            $content = $this->remove_depend("depend","customer.country",$content);
        }

        //     Variables Product

        $productName = "";

        $sku = "";

        if($cData != ""){
            if($cData['sku'] != ""){
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$cData['sku']);
                $productName = $product->getName();
                $sku = $cData['sku'];
            }
        }

        if($productName != null){
            $content = $this->replace_depend("depend","product.name",$content);
            if(strpos($content, "{{var product.name}}"))
                $content = str_replace("{{var product.name}}", $productName, $content);
        }
        else{
            $content = $this->remove_depend("depend","product.name",$content);
        }

        if($sku != null){
            $content = $this->replace_depend("depend","product.sku",$content);
            if(strpos($content, "{{var product.sku}}"))
                $content = str_replace("{{var product.sku}}", $sku, $content);
        }
        else{
            $content = $this->remove_depend("depend","product.sku",$content);
        }


        $html = "<style type=\"text/css\">\n%s\n</style>\n%s";
        $link = $store->getUrl('followupemail/index/checkStatus/',array('eid' => $queueId));

        $time_current =  Mage::getModel('core/date')->gmtTimestamp();
        $link = $link.'&mytime='.$time_current;

        $content = Mage::getModel('followupemail/emailqueue')->InsertAnalytics($queueId,$content);

        if(!$preview)
            $content = '<img src="'.$link.'" style="width:1px;height:1px">'.$content;


        if($emailTemplate['template_styles'] == "")
            return $content;

        else

            return sprintf($html, $emailTemplate['template_styles'], $content);

    }

    public function replace_depend( $tag ,$var, $content )
    {
        return preg_replace("/{{".$tag." ".$var."}}(.*){{\/".$tag."}}/isU", "$1", $content);
    }

    public function remove_depend( $tag ,$var, $content )
    {
        return preg_replace("/{{".$tag." ".$var."}}(.*){{\/".$tag."}}/isU", "", $content);
    }

}