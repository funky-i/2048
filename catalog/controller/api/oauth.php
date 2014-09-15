<?php
class ControllerApiOauth extends Controller {
	public function index() {		
		$json = $this->oauth2->handleTokenRequest();

		// $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function verify() {
		$isVerify = $this->oauth2->verifyResourceRequest();
		if ($isVerify) {
			echo "You accessed my APIs!";
		} else {
			echo "Die";
		}
	}

	public function token() {
		$json = $this->oauth2->getToken();
		$this->response->setOutput(json_encode($json));
	}
}