<?php
class ControllerRestLog extends Controller {
	public function index() {
		initHeader();
		$params = getParams();
		$json = [];

		if (isset($params['data']['log_id'])) {
			$log_id = $params['data']['log_id'];
			$branch_id = $params['data']['branch_id'];
			$this->load->model('rest/log');

			$filter = array(				
				'filter' => array('branch_id' => $branch_id)
			);

			$logs = $this->model_rest_log->getLogs($filter);

			$json['log'] = $logs;
		}	
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function add($data = array()) {
		if ($data) {
			$this->load->model('rest/log');

			$log_id = $this->model_rest_log->addLog($data);
			
			$json['log'] = $log_id;
		}
	}

}