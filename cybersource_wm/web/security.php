<?php

define ('HMAC_SHA256', 'sha256');
//define ('SECRET_KEY', '<REPLACE WITH SECRET KEY>');
//define ('SECRET_KEY', '48a0f5f988e94a5882bd5e152eb8e7a6d9382f2137834cd190d35447cf806c6de8f57c2ff6114d299a212d73ffdc4708f07a81833dfa4c41a4aa11758dde9c430640622f4d724e7c93c72c240f59b78f0b2a0872363a40f6b758ca0c53d3bd3c7d2d4cae8cff44f782449edec86f9dc02b03f412ef6c40798108455cd71055ed');
define ('SECRET_KEY', '48a0f5f988e94a5882bd5e152eb8e7a6d9382f2137834cd190d35447cf806c6de8f57c2ff6114d299a212d73ffdc4708f07a81833dfa4c41a4aa11758dde9c430640622f4d724e7c93c72c240f59b78f0b2a0872363a40f6b758ca0c53d3bd3c7d2d4cae8cff44f782449edec86f9dc02b03f412ef6c40798108455cd71055ed');

function sign ($params) {
  return signData(buildDataToSign($params), SECRET_KEY);
}

function signData($data, $secretKey) {
    return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
}

function buildDataToSign($params) {
        $signedFieldNames = explode(",",$params["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
           $dataToSign[] = $field . "=" . $params[$field];
        }
        return commaSeparate($dataToSign);
}

function commaSeparate ($dataToSign) {
    return implode(",",$dataToSign);
}

?>
