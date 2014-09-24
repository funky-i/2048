<?php
class ControllerApiProduct extends Controller {
	private $error;

	public function index() {		
		initHeader();
		$Params = getParams();

		$json = [];

		$this->load->model('catalog/product');

		if (isset($Params['data'])) {
			$product_id = $Params['data']['product_id'];
			$filter_data = $product_id;
		}

		if (isset($filter_data)) {
			$product = $this->model_catalog_product->getProduct($filter_data);

			$data = $this->pattern($product);
			$json = $data;
		}
		
		$this->response->setOutput(json_encode($json));
		
	}

	public function lists() {
		initHeader();
		$Params = getParams();

		$filter_data = [];
		$json = [];

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
			if (isset($data['filter_page']) && isset($data['filter_limit'])) {
				$page = $data['filter_page'];
				$limit = $data['filter_limit'];
				$filter_data['start'] = ($page - 1) * $limit;
				$filter_data['limit'] = $limit;
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

	private function tags($tag) {

		if ($tag['tag']) {
			$tags = explode(',', $tag['tag']);

			foreach ($tags as $tag) {
				$data[] = array(
					'tag'  => trim($tag),
					'href' => $this->url->link('product/search', 'tag=' . trim($tag))
				);
			}
		}

		return $data;
	}

	private function pattern($data) {
		$media = [];
		$this->load->model('tool/image');

		$galleries = $this->model_catalog_product->getProductImages($data['product_id']);
		$attributes = $this->model_catalog_product->getProductAttributes($data['product_id']);

		if ($galleries) {
			foreach ($galleries as $image) {
				$media[] = array(
					'product_image_id' => $image['product_image_id'],
					'image' => HTTP_SERVER . 'image/' . $image['image'],
					'thumb' => $this->image->resize_to_width($image['image'], 100),
					'sort_order' => $image['sort_order'],
				);
			}
		}

		$result = array(
			'product_id' => $data['product_id'],			
			'sku' => $data['sku'],
			'model' => $data['model'],
			'name' => strip_tags(html_entity_decode(removeHtml($data['name']), ENT_QUOTES, 'UTF-8')),
			'tags' => (isset($data['tags']) && !empty($data['tags']))? $this->tags($data['tags']) : '',
			'short' => utf8_substr(strip_tags(html_entity_decode(removeHtml($data['description']), ENT_QUOTES, 'UTF-8')), 0, 200),
			'description' => strip_tags(html_entity_decode(removeHtml($data['description']), ENT_QUOTES, 'UTF-8')),
			'model' => $data['model'],
			'quantity' => $data['quantity'],
			'image' => ($data['image'])? HTTP_SERVER . 'image/' . $data['image'] : '',
			'thumb' => $this->image->resize_to_width($data['image'], 340),
			'gallery' => $media,
			'attribute' => $attributes,
			// 'thumb' => $this->model_tool_image->resize($data['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')),
			'specification' => '',
			'minimum' => $data['minimum'],
			'price' => $data['price'],
			'special' => $data['special'],
			'rating' => $data['rating'],
			'reward' => $data['reward'],
			'points' => $data['points'],
			'view' => $data['viewed'],
			'review' => $data['reviews'],
			'status' => $data['status'],
			'stock_status' => $data['stock_status'],
			'date_added' => strtotime($data['date_added']),
			'date_modified' => strtotime($data['date_modified'])
		);

		return $result;
	}

}