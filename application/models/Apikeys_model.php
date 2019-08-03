<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apikeys_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('session');

	}

	public function get_key($user){
		error_log("get_key($user)");
		$query = $this->db->get_where('api_keys', array('id_user' => $user));
		if($query->num_rows() == 0){
			error_log("now_rows = " . $query->num_rows());
			return 0;
		}

		$row = $query->row_array();
		error_log(print_r($row, true));

		return $row["nyckel"];
	}

	public function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZåäöÅÄÖ_')
	{
		$pieces = [];
		$max = mb_strlen($keyspace, '8bit') - 1;
		for ($i = 0; $i < $length; ++$i) {
			$pieces []= $keyspace[random_int(0, $max)];
		}
		return implode('', $pieces);
	}

	public function make_my_key($user){
		$string = $this->random_str(60);

		$data = array('id_user' => $user, 'nyckel' => $string);
		$res = $this->db->insert('api_keys', $data);
		return $res;
	}

	public function delete_my_keys($user){
		$sql = "DELETE FROM api_keys WHERE id_user = $user";
		$res = $this->db->query($sql);
		return $res;
	}
}
