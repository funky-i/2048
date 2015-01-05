<?php

// Adjust to your timezone
date_default_timezone_set('Asia/Bangkok');

error_reporting(-1);

// DEVELOPMENT
define('PUSH_PASSPHRASE_DEV', 'lllppp88');
define('PUSH_PERMISSION_DEV', 'cert/ck_scboffice_dev.pem');

require_once 'system/ApnsPHP/Autoload.php';

// $push = new ApnsPHP_Push(
// 	ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
// 	'server_certificates_bundle_sandbox.pem'
// );
$push = new ApnsPHP_Push(
	ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
	PUSH_PERMISSION_DEV
);

$push->setProviderCertificatePassphrase(PUSH_PASSPHRASE_DEV);
// $push->setRootCertificationAuthority('cert/scboffice_aps_dev_key.pem');
$push->connect();

$device = array(
	'14d3c3a4c21cae3caa324a35e19c7e7b938a5201c5b0c812b15e1de5e0f83cff'
);

foreach ($device as $token) {

	$message = new ApnsPHP_Message($token);
	$message->setCustomIdentifier($token);
	$message->setBadge(1);
	$message->setText('Hello APNs-enabled device!');
	$message->setSound();

	$message->setCustomProperty('url', array('bang', 'whiz'));
	$message->setCustomProperty('params', array('bing', 'bong'));

	$message->setExpiry(30);

	$push->add($message);
}

$push->send();
$push->disconnect();


// Examine the error message container
$aErrorQueue = $push->getErrors();
if (!empty($aErrorQueue)) {
	print_r($aErrorQueue);
}
