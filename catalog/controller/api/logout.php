<?php
class ControllerApiLogout extends Controller {
	public function index() {
		initHeader();
		$json = [];

		// $this->customer->logout();
		
		unset($this->session->data['cart']);
		unset($this->session->data['shipping_address']);		
		unset($this->session->data['shipping_method']);
		unset($this->session->data['shipping_methods']);
		unset($this->session->data['payment_address']);
		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['reward']);

		$json['success'] = 'Log Out';
		$this->response->setOutput(json_encode($json));

	}

}