<?php
class ControllerRestLogout extends Controller {
	public function index() {
		initHeader();
		$json = [];

		$this->customer->logout();
		
		unset($this->session->data['cart']);
		unset($this->session->data['shipping_address']);		
		unset($this->session->data['shipping_method']);
		unset($this->session->data['shipping_methods']);
		unset($this->session->data['payment_address']);
		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['reward']);

		unset($this->session->data['facebook_token']);
		unset($this->session->data['facebook_session']);

		$this->load->language('rest/logout');
		$json['error'] = error(array('status' => false, 'description' => $this->language->get('text_success')));

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

}