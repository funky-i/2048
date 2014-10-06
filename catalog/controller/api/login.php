<?php
class ControllerApiLogin extends Controller {
	public function index() {
		initHeader();
		$Params = getParams();
		$json = [];

		$this->load->language('api/login');

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

				$json['success'] = $this->language->get('text_success');
			}else {
				$json['error'] = $this->language->get('error_login');
			}

			$this->response->setOutput(json_encode($json));

		}
	}

}