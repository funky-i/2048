<?php
class ControllerApiCart extends Controller {
	public function index() {
		
		initHeader();
		$Params = getParams();

		$json = [];

		$this->load->language('api/cart');

		if (isset($Params['data']['product_id'])) {
			$this->load->model('catalog/product');
			$data = $Params['data'];
			$product_info = $this->model_catalog_product->getProduct($data['product_id']);

			if ($product_info) {
				if (isset($this->request->post['quantity'])) {
					$quantity = $this->request->post['quantity'];
				} else {
					$quantity = 1;
				}

				if (isset($data['option'])) {
					$option = array_filter($data['option']);
				} else {
					$option = array();
				}

				$this->cart->add($data['product_id'], $quantity, $option);				
				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function add() {
		initHeader();
		$Params = getParams();

		$this->load->language('api/cart');

		$json = [];

		if (isset($Params['data']['product_id'])) {
			$data = $Params['data'];
			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($data['product_id']);

			if ($product_info) {
				if (isset($data['quantity'])) {
					$quantity = $data['quantity'];
				} else {
					$quantity = 1;
				}

				if (isset($data['option'])) {
					$option = array_filter($data['option']);
				} else {
					$option = array();
				}

				if (!isset($data['override']) || !$data['override']) {
					$product_options = $this->model_catalog_product->getProductOptions($data['product_id']);

					foreach ($product_options as $product_option) {
						if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
							$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
						}
					}
				}

				if (!isset($json['error']['option'])) {
					$this->cart->add($data['product_id'], $quantity, $option);

					$json['success'] = $this->language->get('text_success');

					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
				}
			} else {
				$json['error']['store'] = $this->language->get('error_store');
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function edit() {
		initHeader();
		$Params = getParams();

		$json = [];

		$this->load->language('api/cart');
		
		$data = $Params['data'];

		if (isset($Params['data']['key']) && isset($Params['data']['quantity'])) {
			$this->cart->update($data['key'], $data['quantity']);

			$json['success'] = $this->language->get('text_success');

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);
		}
		
		$this->response->setOutput(json_encode($json));
	}

	public function remove() {
		initHeader();
		$Params = getParams();

		$this->load->language('api/cart');

		$json = [];

		if (isset($Params['data']['product_id'])) {
			$data = $Params['data'];
			// Remove
			if (isset($data['key'])) {
				$this->cart->remove($data['key']);

				unset($this->session->data['vouchers'][$data['key']]);

				$json['success'] = $this->language->get('text_success');

				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['reward']);
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}

	public function products() {
		initHeader();

		$this->load->language('api/cart');

		$json = array();

		// Stock
		if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
			$json['error']['stock'] = $this->language->get('error_stock');
		}

		// Products
		$json['products'] = array();

		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $item) {
				if ($item['product_id'] == $product['product_id']) {
					$product_total += $item['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['error']['minimum'][] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			}

			$option_data = array();

			foreach ($product['option'] as $option) {
				$option_data[] = array(
					'product_option_id'       => $option['product_option_id'],
					'product_option_value_id' => $option['product_option_value_id'],
					'name'                    => $option['name'],
					'value'                   => $option['value'],
					'type'                    => $option['type']
				);
			}

			$json['products'][] = array(
				'key'        => $product['key'],
				'product_id' => $product['product_id'],
				'name'       => $product['name'],
				'model'      => $product['model'],
				'option'     => $option_data,
				'quantity'   => $product['quantity'],
				'stock'      => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
				'shipping'   => $product['shipping'],
				'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
				'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']),
				'reward'     => $product['reward']
			);
		}

		// Voucher
		$json['vouchers'] = array();

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$json['vouchers'][] = array(
					'code'             => $voucher['code'],
					'description'      => $voucher['description'],
					'from_name'        => $voucher['from_name'],
					'from_email'       => $voucher['from_email'],
					'to_name'          => $voucher['to_name'],
					'to_email'         => $voucher['to_email'],
					'voucher_theme_id' => $voucher['voucher_theme_id'],
					'message'          => $voucher['message'],
					'amount'           => $this->currency->format($voucher['amount'])
				);
			}
		}

		// Totals
		$this->load->model('extension/extension');

		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();

		$sort_order = array();

		$results = $this->model_extension_extension->getExtensions('total');

		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
		}

		array_multisort($sort_order, SORT_ASC, $results);

		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('total/' . $result['code']);

				$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
			}
		}

		$sort_order = array();

		foreach ($total_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $total_data);

		$json['totals'] = array();

		foreach ($total_data as $total) {
			$json['totals'][] = array(
				'title' => $total['title'],
				'text'  => $this->currency->format($total['value'])
			);
		}
		
		$this->response->setOutput(json_encode($json));
	}

	public function clear() {
		initHeader();

		unset($this->session->data['cart']);
		unset($this->session->data['shipping_address']);		
		unset($this->session->data['shipping_method']);
		unset($this->session->data['shipping_methods']);
		unset($this->session->data['payment_address']);
		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['reward']);
	}
}