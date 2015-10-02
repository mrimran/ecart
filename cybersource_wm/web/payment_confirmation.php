<?php include 'security.php' ?>

<html>
    <head>
        <title>Secure Acceptance</title>
    </head>
    <body>
        <div align="center">
            <br />
            <br />
            <br />
            <br />
            <br />

            <img src="loading.gif" />

        </div>
        <form id="payment_confirmation" action="https://testsecureacceptance.cybersource.com/pay" name="payment_confirmation" method="post">
        <?php
        //$allowedFields = array('access_key', 'profile_id', 'transaction_uuid', 'unsigned_field_names', 'signed_date_time', 'locale', 'transaction_type', 'reference_number', 'amount', 'currency', 'frontend', 'frontend_cid', 'external_no_cache', 'adminhtml', 'adminhtml_cid');
        //$allowedFields = array('access_key', 'profile_id', 'transaction_uuid', 'unsigned_field_names', 'signed_date_time', 'locale', 'transaction_type', 'reference_number', 'amount', 'currency');
        //$params['signed_field_names'] = "signed_field_names,".implode(",", $allowedFields);

	$signed = 'access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type,reference_number,amount,currency,bill_to_address_city,bill_to_address_country,bill_to_address_line1,bill_to_address_line2,bill_to_address_postal_code,bill_to_address_state,bill_to_company_name,bill_to_email,bill_to_forename,bill_to_surname,bill_to_phone,customer_ip_address';
	    $extraFormFields = 'merchant_defined_data1,merchant_defined_data2,merchant_defined_data3,merchant_defined_data5,merchant_defined_data6,merchant_defined_data7,merchant_defined_data9,merchant_defined_data10,merchant_defined_data11,merchant_defined_data12,merchant_defined_data13,merchant_defined_data14,merchant_defined_data18,merchant_defined_data19,merchant_defined_data21,merchant_defined_data25';
	    //merchant_defined_data8 culpriti coma seprated values not allowed
	    $signed = $signed . ','  . $extraFormFields;
	    $allowedFields = explode(",",$signed);
	    //print_r($allowedFields);
	    foreach($_POST as $name => $value) {
	        if(in_array($name, $allowedFields))  {
                    $value = trim(preg_replace('/[^A-Za-z0-9\-\s\@#:~!^*_+=`\."\']/', '', strip_tags($value)));
                    $params[$name] = str_replace("  ", " ", $value);//remove double spaces
                }
	    }

	    $params['signed_field_names'] = implode(",", $allowedFields);
	    $params['signed_date_time'] = gmdate("Y-m-d\TH:i:s\Z");
        
	      /* echo "<pre>";
          print_r($params);
          echo "</pre>"; */
        ?>
        <?php
        foreach ($params as $name => $value) {
            echo "<input type=\"hidden\" id=\"" . $name . "\" name=\"" . $name . "\" value=\"" . $value . "\"/>\n";
        }
        echo "<input type=\"hidden\" id=\"signature\" name=\"signature\" value=\"" . sign($params) . "\"/>\n";
//die();
        ?>
    </form>
    <script>
    window.onload = function(){
        //document.forms['payment_confirmation'].submit();
		document.createElement('form').submit.call(document.payment_confirmation);
    }
    </script>
</body>
</html>
