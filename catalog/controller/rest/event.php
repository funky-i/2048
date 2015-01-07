<?php
class ControllerRestEvent extends Controller {
	public function index() {
		initHeader();
		$json = [];

		$params = getParams();

		if (isset($params['data']['name']) && isset($params['data']['trigger']) && isset($params['data']['route'])) {
			$this->load->model('extension/event');
			
			$name = $params['data']['name'];
			$trigger = $params['data']['trigger'];
			$route = $params['data']['route'];

			$filter = array(
				'code' => $name,
				'trigger' => $trigger
			);

			$event = $this->model_extension_event->getEvent($filter);

			if (!$event) {			
				$this->model_extension_event->addEvent($name, $trigger, $route);
				$json['error'] = error(array('status' => false, 'description' => 'Success: has been add this event!!'));
			} else {
				$json['error'] = error(array('status' => true, 'description' => 'Error: already add this event!!'));
			}
			
			// $this->event->trigger('post.rest.event.add',  $filter);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function event($data = array()) {
		// console($data);
	}

	public function delete() {
		initHeader();
		$json = [];

		$params = getParams();

		if (isset($params['data']['name'])) {
			$this->model_extension_event->deleteEvent($params['data']['name']);	
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}