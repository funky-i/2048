<?php
class ModelPaymentPPStandard extends Model {
	public function getConfig($data = array()) {
		$result = [];

		$result['testmode'] = $this->config->get('pp_standard_test');
		
		if (!$this->config->get('pp_standard_test')) {
			$result['action'] = 'https://www.paypal.com/cgi-bin/webscr';
		} else {
			$result['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}
		
		$result['business'] = $this->config->get('pp_standard_email');
		$result['degug'] = $this->config->get('pp_standard_debug');
		// $result['total'] = $this->config->get('pp_standard_total');
		
		$result['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

		if (!$this->config->get('pp_standard_transaction')) {
			$result['paymentaction'] = 'authorization';
		} else {
			$result['paymentaction'] = 'sale';
		}

		return $result;

	}
	
	public function getMethod($address, $total) {
		$this->load->language('payment/pp_standard');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('pp_standard_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('pp_standard_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('pp_standard_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$currencies = array(
			'AUD',
			'CAD',
			'EUR',
			'GBP',
			'JPY',
			'USD',
			'NZD',
			'CHF',
			'HKD',
			'SGD',
			'SEK',
			'DKK',
			'PLN',
			'NOK',
			'HUF',
			'CZK',
			'ILS',
			'MXN',
			'MYR',
			'BRL',
			'PHP',
			'TWD',
			'THB',
			'TRY'
		);

		if (!in_array(strtoupper($this->currency->getCode()), $currencies)) {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'pp_standard',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('pp_standard_sort_order')
			);
		}

		return $method_data;
	}
}