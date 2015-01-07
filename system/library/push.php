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
	private $push_status;

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');

		$environment = array(
			'developer' => $env = ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
			'production' => $env = ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION
		);
		
		$devmode = $this->config->get('push_mode');
		$this->push_cert_pass = $this->config->get('push_password');
		$this->push_cert_pem = $this->config->get('push_pem');
		$this->push_status = $this->config->get('push_status');

		// DEVELOPMENT
		if ($devmode) {			
			$this->push_environment = $environment['developer'];
		} else {
			$this->push_environment = $environment['production'];
		}

	}

	public function addDevice($data) {
		
		if(empty($data['os']))
			$data['os'] = 'unknown';

		$sql = "SELECT * FROM " . DB_PREFIX . "customer_to_device";
		$sql .= " WHERE device_token = '" . $this->db->escape($data['device_token']). "'";
		$query = $this->db->query($sql);

		//if no record for the latest one, insert it
		if (empty($query->rows)) {

			//DO INSERT new record
			$sql = "INSERT INTO " . DB_PREFIX . "customer_to_device SET device_token = '" . $this->db->escape($data['device_token']) . "'";
			$sql .= ", customer_id = '" . (int)$data['customer_id'] . "'";
			$sql .= ", os = '" . $this->db->escape($data['os']) . "'";
			$sql .= ", status = 1, date_added = NOW(), date_modified = NOW()";

			$this->db->query($sql);
		}

	}

	public function updateDeviceToken($data) {

		$sql = "SELECT * FROM " . DB_PREFIX . "customer_to_device";
		$sql .= " WHERE device_token = '" . $this->db->escape($data['device_token']). "'";
		$sql .= " AND customer_id = '" . (int)$data['customer_id'] . "'";
		$query = $this->db->query($sql);

		//if no record for the latest one, insert it
		if (!empty($query->rows)) {
			//DO INSERT new record
			$sql = "UPDATE customer_to_device SET customer_id = '" . $data['customer_id'] . "' WHERE device_token = '" . $this->db->escape($data['device_token']) . "'";
			$this->db->query($sql);
		}
		
	}

	public function getDeviceToken($os = 'ios') {
		$sql = "SELECT * FROM customer_to_device WHERE -`os` = '" . $os . "'";
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function send($data) {
		
		// $this->PUSH_PERMISSION = PRODUCTION_ENV ? PUSH_PERMISSION_PROD : PUSH_PERMISSION_DEV;
		// $this->PUSH_PASSPHRASE = PRODUCTION_ENV ? PUSH_PASSPHRASE_PROD : PUSH_PASSPHRASE_DEV;

		$ios = $this->getDeviceToken('ios');
		// $android = $this->getDeviceToken('android');

		// $log_id = $this->addLogMessage($data);		
		// $this->sendGoogleCloudMessage($android,$data,$log_id);		

		$this->pushToAPNS($ios, $data);
	}

	public function pushToAPNS($devices, $msg, $data = array()) {
		
		$push = new ApnsPHP_Push(
			$this->push_environment,
			$this->push_cert_pem
		);

		if ($this->config->get('push_authentication')) {
			$push->setProviderCertificatePassphrase($this->push_cert_pass);
			// $push->setRootCertificationAuthority('cert/scboffice_aps_dev_key.pem');		
		}		
		
		$push->connect();		

		foreach ($devices as $device) {
			
			if (isset($device['message']))
				$msg = $device['message'];

			$message = new ApnsPHP_Message($device['device_token']);
			$message->setCustomIdentifier($device['device_token']);

			if (isset($device['badge']))
				$message->setBadge($device['badge']);

			$message->setText($msg);
			$message->setSound();

			// $message->setCustomProperty('url', array('bang', 'whiz'));
			// $message->setCustomProperty('params', array('bing', 'bong'));

			$message->setExpiry(15);

			$push->add($message);
		}

		if ($this->push_status) {
			$push->send();
			$push->disconnect();
		}
		
		//Error message container
		$pushErr = $push->getErrors();
		if (!empty($pushErr)) {
			$this->pustLogg($pushErr);
		}
	}

	private function pustLogg($error) {
		$this->log->write('Push::' . $error);
	}


}