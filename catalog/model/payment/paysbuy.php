<?php 
class ModelPaymentPaysbuy extends Model {
  public function getConfig($data = array()) {
    $result = [];

    $result['testmode'] = $this->config->get('paysbuy_test');
    
    if (!$this->config->get('paysbuy_test')) {
      $action = 'https://www.paysbuy.com/paynow.aspx';
    } else {
      $action = 'http://demo.paysbuy.com/paynow.aspx';
    }

    $action .= '?lang=' . $this->config->get('paysbuy_language');
    
    $result['action'] = $action;
    $result['psbid'] = $this->config->get('paysbuy_id');
    $result['username'] = $this->config->get('paysbuy_username');
    $result['securecode'] = $this->config->get('paysbuy_securecode');
    $result['language_code'] = $this->config->get('paysbuy_language');

    if (isset($data['currency_code'])) {
      $result['currencyCode'] = $this->currency_code($data['currency_code']);
    } else {
      $result['currencyCode'] = $this->currency_code($this->config->get('paysbuy_currency'));
    }

    $result['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');    

    return $result;

  }

  public function currency_code($code) {

    $currency_code = array(
      'THB' => '764',
      'AUD' => '036',
      'GBP' => '826',
      'EUR' => '978',
      'HKD' => '344',
      'JPY' => '392',
      'NZD' => '554',
      'SGD' => '702',
      'CHF' => '756',
      'USD' => '840'
    );

    if (array_key_exists($code, $currency_code)) {
      $currencyCode = $currency_code[$code];
    } else {
      $currencyCode = $code;
    }

    return $currencyCode;
  }

	public function getMethod($address, $total) {
    
    $this->load->language('payment/paysbuy');

    $method_data = array();

    $method_data = array( 
      'code' => 'paysbuy',
      'title' => $this->language->get('text_title'),
      'terms' => '',
      'sort_order' => $this->config->get('paysbuy_sort_order')
    );

    return $method_data;
	}
}
?>