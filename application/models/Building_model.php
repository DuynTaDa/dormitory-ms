<?php

class Building_model extends CI_Model {

	protected $table = 'building';
	protected $id_name = 'building_id';

	public function __construct() {
		parent::__construct();
	}

	public function getAll() {
		$query = $this->db->get($table);
		return $query->result_array();
	}

	public function add($params) {
		$this->db->insert($table, $params);
	}

	public function edit($params, $id) {
		$this->db->where($id_name, $id);
		$this->db->update($table, $params);
	}
} 