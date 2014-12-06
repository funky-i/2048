<?php 
class ControllerPaymentpaysbuy extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/paysbuy');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('paysbuy', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));

		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_authorization'] = $this->language->get('text_authorization');
		$data['text_sale'] = $this->language->get('text_sale');
		
		$data['entry_id'] = $this->language->get('entry_id');
		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_securecode'] = $this->language->get('entry_securecode');
		$data['entry_test'] = $this->language->get('entry_test');
		$data['entry_language'] = $this->language->get('entry_language');
		$data['entry_total'] = $this->language->get('entry_total');	
		$data['entry_currency'] = $this->language->get('entry_currency');		
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_fail_status'] = $this->language->get('entry_fail_status');
		$data['entry_process_status'] = $this->language->get('entry_process_status');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$data['help_test'] = $this->language->get('help_test');
		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_status'] = $this->language->get('tab_status');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

 		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}
		
 		if (isset($this->error['id'])) {
			$data['error_id'] = $this->error['id'];
		} else {
			$data['error_id'] = '';
		}
		
 		if (isset($this->error['securecode'])) {
			$data['error_securecode'] = $this->error['securecode'];
		} else {
			$data['error_securecode'] = '';
		}

		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/paysbuy', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
		$data['action'] = $this->url->link('payment/paysbuy', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['paysbuy_id'])) {
			$data['paysbuy_id'] = $this->request->post['paysbuy_id'];
		} else {
			$data['paysbuy_id'] = $this->config->get('paysbuy_id');
		}
		
		if (isset($this->request->post['paysbuy_username'])) {
			$data['paysbuy_username'] = $this->request->post['paysbuy_username'];
		} else {
			$data['paysbuy_username'] = $this->config->get('paysbuy_username');
		}
				
		if (isset($this->request->post['paysbuy_securecode'])) {
			$data['paysbuy_securecode'] = $this->request->post['paysbuy_securecode'];
		} else {
			$data['paysbuy_securecode'] = $this->config->get('paysbuy_securecode');
		}
		
		if (isset($this->request->post['paysbuy_test'])) {
			$data['paysbuy_test'] = $this->request->post['paysbuy_test'];
		} else {
			$data['paysbuy_test'] = $this->config->get('paysbuy_test');
		}
		
		if (isset($this->request->post['paysbuy_currency'])) {
			$data['paysbuy_currency'] = $this->request->post['paysbuy_currency'];
		} else {
			$data['paysbuy_currency'] = $this->config->get('paysbuy_currency');
		}
		
		if (isset($this->request->post['paysbuy_total'])) {
			$data['paysbuy_total'] = $this->request->post['paysbuy_total'];
		} else {
			$data['paysbuy_total'] = $this->config->get('paysbuy_total'); 
		} 
				
		if (isset($this->request->post['paysbuy_order_status_id'])) {
			$data['paysbuy_order_status_id'] = $this->request->post['paysbuy_order_status_id'];
		} else {
			$data['paysbuy_order_status_id'] = $this->config->get('paysbuy_order_status_id'); 
		}

		if (isset($this->request->post['paysbuy_fail_status_id'])) {
			$data['paysbuy_fail_status_id'] = $this->request->post['paysbuy_fail_status_id'];
		} else {
			$data['paysbuy_fail_status_id'] = $this->config->get('paysbuy_fail_status_id'); 
		}

		if (isset($this->request->post['paysbuy_process_status_id'])) {
			$data['paysbuy_process_status_id'] = $this->request->post['paysbuy_process_status_id'];
		} else {
			$data['paysbuy_process_status_id'] = $this->config->get('paysbuy_process_status_id'); 
		} 

		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['paysbuy_language'])) {
			$data['paysbuy_language'] = $this->request->post['paysbuy_language'];
		} else {
			$data['paysbuy_language'] = $this->config->get('paysbuy_language'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['paysbuy_status'])) {
			$data['paysbuy_status'] = $this->request->post['paysbuy_status'];
		} else {
			$data['paysbuy_status'] = $this->config->get('paysbuy_status');
		}
		
		if (isset($this->request->post['paysbuy_sort_order'])) {
			$data['paysbuy_sort_order'] = $this->request->post['paysbuy_sort_order'];
		} else {
			$data['paysbuy_sort_order'] = $this->config->get('paysbuy_sort_order');
		}

		$data['languages'][] = array('language_id' => 'T', 'name' => $this->language->get('text_thai'));
		$data['languages'][] = array('language_id' => 'E', 'name' => $this->language->get('text_english'));
		$data['languages'][] = array('language_id' => 'J', 'name' => $this->language->get('text_japanese'));

		$data['currencies'][] = array('currency_id' => 'THB', 'name' => $this->language->get('text_baht'));
		$data['currencies'][] = array('currency_id' => 'USD', 'name' => $this->language->get('text_usd'));
		$data['currencies'][] = array('currency_id' => 'HKD', 'name' => $this->language->get('text_hkd'));
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/paysbuy.tpl', $data));
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/paysbuy')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['paysbuy_id']) {
			$this->error['id'] = $this->language->get('error_id');
		}

		if (!$this->request->post['paysbuy_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['paysbuy_securecode']) {
			$this->error['securecode'] = $this->language->get('error_securecode');
		}
		
		return !$this->error;	
	}
}
?>