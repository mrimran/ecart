<?php
// Magento e-mail tester.
if (!isset($_GET['email'])) {
    echo 'usage: mailtest.php?email=m.uzair@progos.org';
    die;
}

// Send mail, the Magento way:
require_once('app/Mage.php');
Mage::app();

// Create a simple contact form mail:
$emailTemplate = Mage::getModel('core/email_template')
    ->loadDefault('contacts_email_email_template');
$data = new Varien_Object();
$data->setData(    
    array(
        'name' => 'Foo',
        'email' => 'foo@bar.com',
        'telephone' => '123-4567890',
        'comment' => 'This is a test'
    )
);
$vars = array('data' => $data);

// Set sender information:
$storeId = Mage::app()->getStore()->getId();
$emailTemplate->setSenderEmail(
    Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
$emailTemplate->setSenderName(
    Mage::getStoreConfig('trans_email/ident_general/name', $storeId));
$emailTemplate->setTemplateSubject('Test mail');

// Send the mail:
$output = $emailTemplate->send($_GET['email'], null, $vars);
var_dump($output);