<?php
class ControllerApiPayment extends Controller {
	public function index() {
		initHeader();
		$Params = getParams();
		$json = [];

		if (isset($Params['data']['payment_address'])) {
			$this->session->data['payment_address'] = $Params['data']['payment_address'];
		}

		$this->load->language('api/payment');

		// Delete past shipping methods and method just in case there is an error
		unset($this->session->data['payment_methods']);
		unset($this->session->data['payment_method']);

		// Payment Address
		if (!isset($this->session->data['payment_address'])) {
			$json['error'] = $this->language->get('error_address');
		}

		if (!$json) {
			// Totals
			$total = $this->cart->getSubTotal();

			// Payment Methods
			$json['payment_methods'] = array();

			$this->load->model('extension/extension');

			$results = $this->model_extension_extension->getExtensions('payment');

			$recurring = $this->cart->hasRecurringProducts();

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('payment/' . $result['code']);

					$method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

					if ($method) {
						if ($recurring) {
							if (method_exists($this->{'model_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_payment_' . $result['code']}->recurringPayments()) {
								$json['payment_methods'][$result['code']] = $method;
							}
						} else {
							$json['payment_methods'][$result['code']] = $method;
						}
					}
				}
			}

			$sort_order = array();

			foreach ($json['payment_methods'] as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $json['payment_methods']);

			if ($json['payment_methods']) {
				$this->session->data['payment_methods'] = $json['payment_methods'];
			} else {
				$json['error'] = $this->language->get('error_no_payment');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}

	public function callback() {
		initHeader();
		$Params = getParams();
		$json = [];

		if (isset($Params['data']['order_id'])) {
			$order_id = $Params['data']['order_id'];
			$code = isset($this->session->data['payment_method']['code'])? $this->session->data['payment_method']['code'] : '';

			if ($code == 'cod') {
				$this->load->model('checkout/order');

				$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('cod_order_status_id'));

				$json['success'] = $order_id;
			}

			if ($code == 'pp_standard') {

			}
		}	
		
		$this->response->setOutput(json_encode($json));

	}

	public function load() {
		initHeader();
		$Params = getParams();
		$json = [];

		$this->load->model('checkout/order');
		$this->load->model('account/order');

		if (isset($Params['data']['order_id'])) {
			$order_id = $Params['data']['order_id'];
			
			$order_info = $this->model_checkout_order->getOrder($order_id);
		}

		if (isset($order_info)) {
			$SubTotal = 0;
			$payment_code = $order_info['payment_code'];

			$this->language->load('payment/' . $payment_code);
			$this->load->model('payment/' . $payment_code);
			$data = $this->{'model_payment_' . $payment_code}->getConfig();
			
			$data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			$data['products'] = array();
			$products = $this->model_account_order->getOrderProducts($order_id);

			// foreach ($this->cart->getProducts() as $product) {
			foreach ($products as $product) {	
				$option_data = array();

				if (isset($product['option'])) {
					foreach ($product['option'] as $option) {
						if ($option['type'] != 'file') {
							$value = $option['option_value'];
						} else {
							$filename = $this->encryption->decrypt($option['option_value']);

							$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
						}

						$option_data[] = array(
							'name'  => $option['name'],
							'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
						);
					}
				}					

				$data['products'][] = array(
					'name'     => htmlspecialchars($product['name']),
					'model'    => htmlspecialchars($product['model']),
					'price'    => $this->currency->format($product['price'], $order_info['currency_code'], false, false),
					'quantity' => $product['quantity'],
					'option'   => $option_data,
					'weight'   => (isset($product['weight']))? $product['weight'] : 0
				);

				$SubTotal += ($product['price'] * $product['quantity']);
			}

			$data['discount_amount_cart'] = 0;

			$total = $this->currency->format($order_info['total'] - $SubTotal, $order_info['currency_code'], false, false);

			if ($total > 0) {
				$data['products'][] = array(
					'name'     => $this->language->get('text_total'),
					'model'    => '',
					'price'    => $total,
					'quantity' => 1,
					'option'   => array(),
					'weight'   => 0
				);
			} else {
				$data['discount_amount_cart'] -= $total;
			}

			$data['currency_code'] = $order_info['currency_code'];
			$data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
			$data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
			$data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
			$data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
			$data['zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
			$data['country'] = $order_info['payment_iso_code_2'];
			$data['email'] = $order_info['email'];
			$data['invoice'] = $order_id . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$data['lc'] = $this->session->data['language'];
			$data['return'] = $this->url->link('checkout/success');
			$data['notify_url'] = $this->url->link('payment/pp_standard/callback', '', 'SSL');
			$data['cancel_return'] = $this->url->link('checkout/checkout', '', 'SSL');	

			$data['custom'] = $order_id;

			$json['payment'] = $data;
		}

		$this->response->setOutput(json_encode($json));

	}
	
	/*
	public function loaded() {
		$order_id = 9;
		$this->language->load('payment/pp_standard');

		$data['text_testmode'] = $this->language->get('text_testmode');
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['testmode'] = $this->config->get('pp_standard_test');

		if (!$this->config->get('pp_standard_test')) {
			$data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
		} else {
			$data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if ($order_info) {
			$data['business'] = $this->config->get('pp_standard_email');
			$data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			$data['products'] = array();

			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['option_value'];
					} else {
						$filename = $this->encryption->decrypt($option['option_value']);

						$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}

				$data['products'][] = array(
					'name'     => htmlspecialchars($product['name']),
					'model'    => htmlspecialchars($product['model']),
					'price'    => $this->currency->format($product['price'], $order_info['currency_code'], false, false),
					'quantity' => $product['quantity'],
					'option'   => $option_data,
					'weight'   => $product['weight']
				);
			}

			$data['discount_amount_cart'] = 0;

			$total = $this->currency->format($order_info['total'] - $this->cart->getSubTotal(), $order_info['currency_code'], false, false);

			if ($total > 0) {
				$data['products'][] = array(
					'name'     => $this->language->get('text_total'),
					'model'    => '',
					'price'    => $total,
					'quantity' => 1,
					'option'   => array(),
					'weight'   => 0
				);
			} else {
				$data['discount_amount_cart'] -= $total;
			}

			$data['currency_code'] = $order_info['currency_code'];
			$data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
			$data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
			$data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
			$data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
			$data['zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
			$data['country'] = $order_info['payment_iso_code_2'];
			$data['email'] = $order_info['email'];
			$data['invoice'] = $order_id . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$data['lc'] = $this->session->data['language'];
			$data['return'] = $this->url->link('checkout/success');
			$data['notify_url'] = $this->url->link('payment/pp_standard/callback', '', 'SSL');
			$data['cancel_return'] = $this->url->link('checkout/checkout', '', 'SSL');

			if (!$this->config->get('pp_standard_transaction')) {
				$data['paymentaction'] = 'authorization';
			} else {
				$data['paymentaction'] = 'sale';
			}

			$data['custom'] = $order_id;

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pp_standard.tpl')) {
				// $this->load->view($this->config->get('config_template') . '/template/payment/pp_standard.tpl', $data);
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/pp_standard.tpl', $data));
			} else {
				$this->load->view('default/template/payment/pp_standard.tpl', $data);
			}
		}
	}
	*/
	/*
	public function address() {
		initHeader();
		$Params = getParams();
		$json = [];	

		$this->load->language('api/payment');

		// Delete old payment address, payment methods and method so not to cause any issues if there is an error
		unset($this->session->data['payment_address']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['payment_method']);

		$json = [];

		// Add keys for missing post vars
		$keys = array(
			'firstname',
			'lastname',
			'company',
			'address_1',
			'address_2',
			'postcode',
			'city',
			'zone_id',
			'country_id'
		);

		foreach ($keys as $key) {
			if (!isset($this->request->post[$key])) {
				$this->request->post[$key] = '';
			}
		}

		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$json['error']['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$json['error']['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128)) {
			$json['error']['address_1'] = $this->language->get('error_address_1');
		}

		if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 32)) {
			$json['error']['city'] = $this->language->get('error_city');
		}

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

		if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10)) {
			$json['error']['postcode'] = $this->language->get('error_postcode');
		}

		if ($this->request->post['country_id'] == '') {
			$json['error']['country'] = $this->language->get('error_country');
		}

		if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
			$json['error']['zone'] = $this->language->get('error_zone');
		}

		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($this->config->get('config_customer_group_id'));

		foreach ($custom_fields as $custom_field) {
			if (($custom_field['location'] == 'address') && $custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
				$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
			}
		}

		if (!$json) {
			$this->load->model('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

			if ($country_info) {
				$country = $country_info['name'];
				$iso_code_2 = $country_info['iso_code_2'];
				$iso_code_3 = $country_info['iso_code_3'];
				$address_format = $country_info['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

			if ($zone_info) {
				$zone = $zone_info['name'];
				$zone_code = $zone_info['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			$this->session->data['payment_address'] = array(
				'firstname'      => $this->request->post['firstname'],
				'lastname'       => $this->request->post['lastname'],
				'company'        => $this->request->post['company'],
				'address_1'      => $this->request->post['address_1'],
				'address_2'      => $this->request->post['address_2'],
				'postcode'       => $this->request->post['postcode'],
				'city'           => $this->request->post['city'],
				'zone_id'        => $this->request->post['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $this->request->post['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format,
				'custom_field'   => isset($this->request->post['custom_field']) ? $this->request->post['custom_field'] : array()
			);

			$json['success'] = $this->language->get('text_address');

			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function methods() {
		$this->load->language('api/payment');

		// Delete past shipping methods and method just in case there is an error
		unset($this->session->data['payment_methods']);
		unset($this->session->data['payment_method']);

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Payment Address
			if (!isset($this->session->data['payment_address'])) {
				$json['error'] = $this->language->get('error_address');
			}

			if (!$json) {
				// Totals
				$total = $this->cart->getSubTotal();

				// Payment Methods
				$json['payment_methods'] = array();

				$this->load->model('extension/extension');

				$results = $this->model_extension_extension->getExtensions('payment');

				$recurring = $this->cart->hasRecurringProducts();

				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('payment/' . $result['code']);

						$method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

						if ($method) {
							if ($recurring) {
								if (method_exists($this->{'model_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_payment_' . $result['code']}->recurringPayments()) {
									$json['payment_methods'][$result['code']] = $method;
								}
							} else {
								$json['payment_methods'][$result['code']] = $method;
							}
						}
					}
				}

				$sort_order = array();

				foreach ($json['payment_methods'] as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $json['payment_methods']);

				if ($json['payment_methods']) {
					$this->session->data['payment_methods'] = $json['payment_methods'];
				} else {
					$json['error'] = $this->language->get('error_no_payment');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function method() {
		$this->load->language('api/payment');

		// Delete old payment method so not to cause any issues if there is an error
		unset($this->session->data['payment_method']);

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Payment Address
			if (!isset($this->session->data['payment_address'])) {
				$json['error'] = $this->language->get('error_address');
			}

			// Payment Method
			if (empty($this->session->data['payment_methods'])) {
				$json['error'] = $this->language->get('error_no_payment');
			} elseif (!isset($this->request->post['payment_method'])) {
				$json['error'] = $this->language->get('error_method');
			} elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
				$json['error'] = $this->language->get('error_method');
			}

			if (!$json) {
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];

				$json['success'] = $this->language->get('text_method');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	*/
}