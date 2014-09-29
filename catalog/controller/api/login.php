<?php
class ControllerApiLogin extends Controller {
	public function index() {
		initHeader();
		$Params = getParams();

		$json = [];

		if (isset($Params['data']['username']) && isset($Params['data']['password']) && isset($Params['data']['token'])) {
			$data = $Params['data'];
			$this->customer->login($data['username'], $data['password']);

			if ($this->customer->isLogged()) {
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
					'custom_field'      => $custom_fields
				);

				$json['success'] = 'Logged in success!!';
			}else {
				$json['error'] = 'Fail: check your username and password again!!';
			}

			$this->response->setOutput(json_encode($json));

		}
	}

	public function login() {
		
		$this->load->language('api/login');

		// Delete old login so not to cause any issues if there is an error
		unset($this->session->data['api_id']);

		$keys = array(
			'username',
			'password'
		);

		foreach ($keys as $key) {
			if (!isset($this->request->post[$key])) {
				$this->request->post[$key] = '';
			}
		}

		$json = array();

		$this->load->model('account/api');

		$api_info = $this->model_account_api->login($this->request->post['username'], $this->request->post['password']);

		if ($api_info) {
			$this->session->data['api_id'] = $api_info['api_id'];

			$json['cookie'] = $this->session->getId();

			$json['success'] = $this->language->get('text_success');
		} else {
			$json['error'] = $this->language->get('error_login');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}