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
        foreach ($_POST as $name => $value) {
            //if(in_array($name, $allowedFields))
            $params[$name] = $value;
        }
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
