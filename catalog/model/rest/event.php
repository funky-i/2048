<?php
class ModelRestEvent extends Model {
	public function getEvent($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "event WHERE `code` = '" . $this->db->escape($data['code']) . "' AND `trigger` = '" . $this->db->escape($data['trigger']) . "'";
		
		$query = $this->db->query($sql);

		return $query->row;
	}

	public function addEvent($code, $trigger, $action) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "event SET `code` = '" . $this->db->escape($code) . "', `trigger` = '" . $this->db->escape($trigger) . "', `action` = '" . $this->db->escape($action) . "'");
	}

	public function deleteEvent($code) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "event WHERE `code` = '" . $this->db->escape($code) . "'");
	}
}