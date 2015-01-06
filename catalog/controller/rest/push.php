<?php
class ControllerRestPush extends Controller {
	public function index() {
		initHeader();
		$params = getParams();
		$json = [];

		
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function send($data = array()) {
		initHeader();
		$params = getParams();
		$json = [];

		if ($params['data']['device_token']) {

			$message = $params['data']['message'];
			$device = $params['data']['device_token'];

			$devices = array(
				array('device_token' => $device, 'message' => $message, 'badge' => 3)
			);

			$this->push->pushToAPNS($devices, $message);

		}
		
	}

}