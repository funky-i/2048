<?php
class ControllerRestPush extends Controller {
	public function index() {
		initHeader();
		$params = getParams();
		$json = [];

		$data = array('message' => "Hello APNs enable!!");
		$this->push->send($data);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	//Manual push notification
	public function send($data = array()) {
		initHeader();
		$params = getParams();
		$json = [];

		$this->language->load('rest/push');

		if (isset($params['data']['device_token'])) {

			$message = (isset($params['data']['message']))? $params['data']['message'] : "Hello APNs Device!!";
			$device_token = $params['data']['device_token'];

			$device[] = array('device_token' => $device_token, 'message' => $message, 'badge' => 1);
			$devices = array(
				'devices' => $device,
				'message' => $message
			);

			$this->push->send($devices);

		} else {
			$errStatus = true;
			$errMessage = $this->language->get('error_push');			
		}

		$json['error'] = error(array('status' => $errStatus, 'description' => $errMessage));
		
	}

	//Add new device token
	public function add() {
		initHeader();
		$params = getParams();
		$json = [];

		$this->language->load('rest/push');

		if (isset($params['data']['device_token']) && (isset($params['data']['customer_id']))) {
			$data = $params['data'];

			$this->load->model('account/customer');
			$customer = $this->model_account_customer->getCustomer($data['customer_id']);

			if ($customer) {
				$inputData = array(
					'customer_id' => $data['customer_id'],
					'device_token' => $data['device_token'],
					'os' => (isset($data['os']))? $data['os'] : '',
					'device_type' => (isset($data['device_type']))? $data['device_type'] : ''
				);
				
				$return = $this->push->addDevice($inputData);

				if ($return) {
					$errStatus = false;
					$errMessage = $this->language->get('text_success');
				} else {
					$errStatus = true;
					$errMessage = $this->language->get('error_device');
				}
			} else {
				$errStatus = true;
				$errMessage = $this->language->get('error_customer');
			}
		} else {
			$errStatus = true;
			$errMessage = $this->language->get('error_add_device');
		}

		$json['error'] = error(array('status' => $errStatus, 'description' => $errMessage));

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}