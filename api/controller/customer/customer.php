<?php
class ControllerCustomerCustomer extends Controller {
	private $error = array();

	public function index() {		

		$this->load->model('sale/customer');

		$this->getList();
	}

}