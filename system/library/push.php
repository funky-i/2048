<?php
class Push {
	private $config;
	private $db;
	private $data = array();

	private $maximum_push_per_day = 10;
	private $default_app_name = 'com.bumbliss.fusiontwo';
	private $push_cert_pass;
	private $push_cert_pem;
	private $push_environment;

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');

		$environment = array(
			'dev' => $env = ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
			'prod' => $env = ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION
		);
		
		$testMode = true;

		if ($testMode) {
			// DEVELOPMENT
			$this->push_cert_pass = 'lllppp88';
			$this->push_cert_pem = 'cert/ck_scboffice_dev.pem';
			$this->push_environment = $environment['dev'];
		}

	}

	public function addDevice($data) {
		
		if(empty($data['os']))
			$data['os'] = 'unknown';

		$sql = "SELECT * FROM " . DB_PREFIX . "customer_device_token";
		$sql .= " WHERE device_token = '" . $this->db->escape($data['device_token']). "'";
		$query = $this->db->query($sql);

		//if no record for the latest one, insert it
		if (empty($query->rows)) {

			//DO INSERT new record
			$sql = "INSERT INTO " . DB_PREFIX . "customer_device_token SET device_token = '" . $this->db->escape($data['device_token']) . "'";
			$sql .= ", customer_id = '" . (int)$data['customer_id'] . "'";
			$sql .= ", os = '" . $this->db->escape($data['os']) . "'";
			$sql .= ", status = 1, date_added = NOW(), date_modified = NOW()";

			$this->db->query($sql);
		}

	}

	public function updateDeviceToken($data) {

		$sql = "SELECT * FROM " . DB_PREFIX . "device_token";
		$sql .= " WHERE device_token = '" . $this->db->escape($data['device_token']). "'";
		$sql .= " AND customer_id = '" . (int)$data['customer_id'] . "'";
		$query = $this->db->query($sql);

		//if no record for the latest one, insert it
		if (!empty($query->rows)) {

			//DO INSERT new record
			$sql = "UPDATE device_token SET customer_id = '".$data['customer_id']."' WHERE device_token = '".$this->db->escape($data['device_token'])."'";

			$this->db->query($sql);
		}
		
	}

	public function getDeviceToken($os = 'ios') {
		$sql = "SELECT * FROM device_token WHERE os = '".$os."'";
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function send($data) {
		
		// $this->PUSH_PERMISSION = PRODUCTION_ENV ? PUSH_PERMISSION_PROD : PUSH_PERMISSION_DEV;
		// $this->PUSH_PASSPHRASE = PRODUCTION_ENV ? PUSH_PASSPHRASE_PROD : PUSH_PASSPHRASE_DEV;

		// $ios = $this->getDeviceToken('ios');
		// $android = $this->getDeviceToken('android');

		// $log_id = $this->addLogMessage($data);

		// $this->pushToAPNS($ios,$data,$log_id);
		// $this->sendGoogleCloudMessage($android,$data,$log_id);

		$ios = [];

		$this->pushToAPNS($ios, $data);
	}

	public function pushToAPNS($devices, $data) {
		
		$push = new ApnsPHP_Push(
			$this->push_environment,
			$this->push_cert_pem
		);

		$push->setProviderCertificatePassphrase($this->push_cert_pass);
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
	}


}