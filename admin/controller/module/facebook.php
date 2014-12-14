<?php
class ControllerModuleFacebook extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/facebook');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_setting_setting->editSetting('facebook', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		
		$data['entry_id'] = $this->language->get('entry_id');
		$data['entry_secret'] = $this->language->get('entry_secret');
		$data['entry_uri'] = $this->language->get('entry_uri');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['help_uri'] = $this->language->get('help_uri');
		$data['help_route'] = $this->language->get('help_route');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/facebook', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('module/facebook', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['facebook_status'])) {
			$data['facebook_status'] = $this->request->post['facebook_status'];
		} else {
			$data['facebook_status'] = $this->config->get('facebook_status');
		}

		if (isset($this->request->post['facebook_id'])) {
			$data['facebook_id'] = $this->request->post['facebook_id'];
		} else {
			$data['facebook_id'] = $this->config->get('facebook_id');
		}

		if (isset($this->request->post['facebook_secret'])) {
			$data['facebook_secret'] = $this->request->post['facebook_secret'];
		} else {
			$data['facebook_secret'] = $this->config->get('facebook_secret');
		}

		if (isset($this->request->post['facebook_uri'])) {
			$data['facebook_uri'] = $this->request->post['facebook_uri'];
		} else {
			$data['facebook_uri'] = $this->config->get('facebook_uri');
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/facebook.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/facebook')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}