<?php
class ModelRestLog extends Model {
	public function addLog($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_log SET task_id = '" . (int)$data['task_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', `option` = '" . $this->db->escape(serialize($data['option'])) . "', date_added = NOW()");

		$this->db->getLastId();

	}

	public function getLogs($data = array()) {
		$logs = [];		

		$sql = "SELECT * FROM customer_log WHERE 1";

		if (isset($data['log_id'])) {
			$sql .= " AND log_id = '" . $data['log_id'] . "'";
		}

		if (isset($data['filter'])) {
			foreach ($data['filter'] as $filter => $filterValue) {
				$sql .= ' AND `option` REGEXP \'.*"' . $filter . '";s:[0-9]+:"' . $filterValue . '".*\'';
			}			
		}

		$query = $this->db->query($sql);

		if ($query->rows) {
			foreach ($query->rows as $value) {

				foreach ($value as $k => $v) {
					if ($k == 'option') {
						$optional = unserialize($v);
						foreach ($optional as $k => $v) {
							$result[$k] = $v;
						}
					} else {
						$result[$k] = $v;
					}
				}

				$logs[] = $result;	
				
			}
		}

		return $logs;
	}

	public function getLog($log_id) {
		$result = [];		

		$sql = "SELECT * FROM customer_log WHERE 1";

		if (isset($data['log_id'])) {
			$sql .= " AND log_id = '" . $data['log_id'] . "'";
		}

		$query = $this->db->query($sql);

		if ($query->row) {
			foreach ($query->row as $key => $value) {
				if ($key == 'option') {
					$optional = unserialize($value);
					foreach ($optional as $k => $v) {
						$result[$k] = $v;
					}					
				} else {
					$result[$key] = $value;
				}
				
			}
		}

		return $result;
	}
}