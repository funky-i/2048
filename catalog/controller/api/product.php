<?php
class ControllerApiProduct extends Controller {
	private $error;

	public function index() {		
		initHeader();
		$Params = getParams();

		$json = array();

		$this->load->model('catalog/product');

		if (isset($Params['data'])) {
			$product_id = $Params['data']['product_id'];
			$filter_data = $product_id;
		}

		if (isset($filter_data)) {
			$product = $this->model_catalog_product->getProduct($filter_data);

			$data[] = $this->pattern($product);
			$json = $data;
		}
		
		$this->response->setOutput(json_encode($json));
		
	}

	public function lists() {
		initHeader();
		$Params = getParams();

		$filter_data = array();
		$json = array();

		if (isset($Params['data'])) {
			$data = $Params['data'];
			

			if (isset($data['filter_name'])) {
				$filter_data['filter_name'] = $data['filter_name'];
			}
			if (isset($data['filter_tag'])) {
				$filter_data['filter_tag'] = $data['filter_tag'];
			}
			if (isset($data['filter_category_id'])) {
				$filter_data['filter_category_id'] = $data['filter_category_id'];
			}
			if (isset($data['filter_manufacturer_id'])) {
				$filter_data['filter_manufacturer_id'] = $data['filter_manufacturer_id'];
			}
		
		}

		$this->load->model('catalog/product');
		
		$products = $this->model_catalog_product->getProducts($filter_data);

		foreach ($products as $product) {
			$data[] = $this->pattern($product);
		}

		$json = $data;

		$this->response->setOutput(json_encode($json));
	}

	private function pattern($data) {
		$result = array(
			'product_id' => $data['product_id'],
			'name' => $data['name'],
			'short' => utf8_substr(strip_tags(html_entity_decode(removeHtml($data['description']), ENT_QUOTES, 'UTF-8')), 0, 200),
			'description' => strip_tags(html_entity_decode(removeHtml($data['description']), ENT_QUOTES, 'UTF-8')),
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