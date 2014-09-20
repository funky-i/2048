<?php
class ControllerApiProduct extends Controller {
	private $error;

	public function index() {		
		initHeader();
		
		$json = array();

		$this->load->model('catalog/product');
		
		$products = $this->model_catalog_product->getProducts();

		foreach ($products as $product) {
			$data[] = $this->pattern($product);
		}

		$json = ($data);
		$this->response->setOutput(json_encode($json));
		
	}

	private function pattern($data) {
		$result = array(
			'product_id' => $data['product_id'],
			'name' => $data['name'],
			'description' => utf8_substr(strip_tags(html_entity_decode(removeHtml($data['description']), ENT_QUOTES, 'UTF-8')), 0, 200),//htmlentities($data['description']),
			'model' => $data['model'],
			'quantity' => $data['quantity'],
			'image' => ($data['image'])? HTTP_SERVER . 'image/' . $data['image'] : '',
			'price' => $data['price'],
			'rating' => $data['rating'],
			'view' => $data['viewed'],
			'review' => $data['reviews'],
			'status' => $data['status'],
			'date_added' => strtotime($data['date_added']),
			'date_modified' => strtotime($data['date_modified'])
		);

		return $result;
	}

}