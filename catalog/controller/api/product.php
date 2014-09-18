<?php
class ControllerApiProduct extends Controller {
	private $error;

	public function index() {
		$json = array();

		$this->load->model('catalog/product');
		
		$json = $this->model_catalog_product->getProducts();

		$this->response->setOutput(json_encode($json));
	}

}