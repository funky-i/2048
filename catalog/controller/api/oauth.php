<?php
class ControllerApiOauth extends Controller {
	public function index() {
		initHeader();
		
		$json = $this->oauth2->handleTokenRequest();

		if (isset($json['access_token']))
			$this->logged();

		$this->response->setOutput(json_encode($json));
		
	}

	public function verify() {
		$isVerify = $this->oauth2->verifyResourceRequest();
		$this->response->setOutput(json_encode($isVerify));
	}

	public function token() {
		$json = $this->oauth2->getToken();
		$this->response->setOutput(json_encode($json));
	}

	private function logged() {
		$user = $this->oauth2->getUser();
		$this->customer->login($user['username'], null, true);

		if ($this->customer->isLogged())
			return true;
	}

}