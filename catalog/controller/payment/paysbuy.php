<?php
class ControllerPaymentPaysbuy extends Controller {	

	public function index() {

		$this->language->load('payment/paysbuy');
		
		$data['text_test_mode'] = $this->language->get('text_test_mode');		
    	$data['text_heading'] = $this->language->get('text_heading');
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['testmode'] = $this->config->get('paysbuy_test');
		
		$this->load->model('checkout/order');
		$this->load->model('payment/paysbuy');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {

			if (!$this->config->get('paysbuy_test')) {    		
	    		$action = 'https://www.paysbuy.com/paynow.aspx';
	  		} else {  			
				$action = 'http://demo.paysbuy.com/paynow.aspx';
			}

			$action .= '?lang=' . $this->config->get('paysbuy_language');

			$data['action'] = $action;
			$data['psbid'] = $this->config->get('paysbuy_id');
			$data['username'] = $this->config->get('paysbuy_username');
			$data['securecode'] = $this->config->get('paysbuy_securecode');
			$data['currencyCode'] = $this->model_payment_paysbuy->currency_code($order_info['currency_code']);
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
					'name'     => $product['name'],
					'model'    => $product['model'],
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
			
			$data['total'] = $this->cart->getTotal();
			$data['currency_code'] = $order_info['currency_code'];
			$data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');	
			$data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');	
			$data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');	
			$data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');	
			$data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');	
			$data['zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');	
			$data['country'] = $order_info['payment_iso_code_2'];
			$data['email'] = $order_info['email'];
			$data['invoice'] = $this->session->data['order_id'] . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			$data['lc'] = $this->session->data['language'];

			$data['return'] = $this->url->link('checkout/success');
			$data['resp_front_url'] = $this->url->link('checkout/success');
			$data['resp_back_url'] = $this->url->link('payment/paysbuy/callback', '', 'SSL');
			$data['cancel_return'] = $this->url->link('checkout/checkout', '', 'SSL');
			$data['custom'] = $this->session->data['order_id'];

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paysbuy.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/payment/paysbuy.tpl', $data);
			} else {
				return $this->load->view('default/template/payment/paysbuy.tpl', $data);
			}
		}
	}
	
	private function getOrderId($result) {
		$status = false;

		if (strlen($result) >= 2) {
			$order_id = substr($result, 2, strlen($result) - 2);
		} else {
			$order_id = 0;
		}

		return $order_id;
	}

	public function callback() {

		$order_id = $this->getOrderId($this->request->post['result']);
		
		$this->load->model('checkout/order');
				
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info) {
			$apCode = $this->request->post['apCode'];
			$result = $this->request->post['result'];
			$paysuby_status = substr($result, 0, 2);

			// status result
			// 00=Success
			// 99=Fail
			// 02=Process

			switch ($paysuby_status) {
				case '00':
					$order_status_id = $this->config->get('paysbuy_order_status_id');
					break;
				case '99':
					$order_status_id = $this->config->get('paysbuy_fail_status_id');
					break;
				case '01':
					$order_status_id = $this->config->get('paysbuy_fail_status_id');
					break;	
				case '02':
					$order_status_id = $this->config->get('paysbuy_process_status_id');
					break;
				default:
					$order_status_id = $this->config->get('paysbuy_fail_status_id');
					break;
			}

			if (!$order_info['order_status_id']) {
				$this->model_checkout_order->confirm($order_id, $order_status_id, $this->request->post['result'], true);
			} else {
				$this->model_checkout_order->update($order_id, $order_status_id, $this->request->post['result'], false);
			}
		}
			
	}
}
?>