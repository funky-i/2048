<?php
class ControllerRestLogin extends Controller {
	public function index() {
		initHeader();
		$params = getParams();
		$json = [];

		$this->load->language('rest/login');

		if (isset($params['data']['username']) && isset($params['data']['password'])) {
			$data = $params['data'];
			$this->customer->login($data['username'], $data['password']);

			if ($this->customer->isLogged()) {
				
				$this->pattern();				
				
				$errStatus = false;
				$errMessage = $this->language->get('text_success');	

				$json['error'] = error(array('status' => false, 'description' => $this->language->get('text_success')));
			}else {
				$errStatus = true;
				$errMessage = $this->language->get('error_login');				
			}
		}

		$json['error'] = error(array('status' => $errStatus, 'description' => $errMessage));

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	private function pattern() {
		$token = $this->oauth2->handleTokenRequest();

		// Custom field validation
		$this->load->model('account/custom_field');
		$custom_fields = $this->model_account_custom_field->getCustomFields($this->customer->getGroupId());

		$this->session->data['customer'] = array(
			'customer_id'       => $this->customer->getId(),
			'customer_group_id' => $this->customer->getGroupId(),
			'firstname'         => $this->customer->getFirstName(),
			'lastname'          => $this->customer->getLastName(),
			'email'             => $this->customer->getEmail(),
			'telephone'         => $this->customer->getTelephone(),
			'fax'               => $this->customer->getFax(),
			'custom_field'      => $custom_fields,
			'token' => $token
		);
	}

}