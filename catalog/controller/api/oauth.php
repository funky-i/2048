<?php
class ControllerApiOauth extends Controller {
	public function index() {
		initHeader();
		
		// if ($this->customer->isLogged()) {
		// 	$json = $this->customer->getId();
		// } else {
		// 	$json = $this->oauth2->handleTokenRequest();
		// }		

		$json = $this->oauth2->handleTokenRequest();
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
		$isLogged = false;

		$user = $this->oauth2->getUser();

		$this->customer->login($user['username'], null, true);

		if ($this->customer->isLogged())
			$isLogged =  true;

		return $isLogged;
	}

}