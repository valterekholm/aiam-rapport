<?php
class Miniblog_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function create(){

	}

	//Hämtar jobb, utan anknyten data
	public function get_posts(){
		error_log("get_posts");

		$query = $this->db->get('mini_blog');
		return $query->result_array();
	}

	public function get_latest_post($company_id = FALSE){
		error_log("get_latest_post");
		$this->db->select('*');
		$this->db->from('mini_blog');
		//$this->db->group_start();
		if(!$company_id){
			error_log("from super admin");
			$this->db->where('company_id IS NULL', null, false);
		}
		else{
			error_log("from company $company_id");
			$this->db->where('company_id IS NOT NULL', null, false);
		}
		//$this->db->group_end();

		$this->db->order_by("created_date","DESC");
		$this->db->limit(1);
		$query=$this->db->get();
		return $query->row_array();
	}

	public function any_admin_post(){
		$sql = "SELECT COUNT(*) c FROM mini_blog WHERE company_id IS NULL";
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->c > 0;
	}
	/*
	public function get_schemas_for_company($company_id){
		$sql = "SELECT DISTINCT js.* FROM jobb_schema js
			JOIN arbets_tillfalle at
			ON (arbetstillfalle_id = at.id)
			LEFT JOIN kunder_arbetsplatser ka
			ON (ka.id_arbetsplats = at.arbetsplats_id)
			LEFT JOIN kund
			ON (kund.id = ka.id_kund)
			LEFT JOIN companies_kunder ck
			ON (ck.id_kund = kund.id) WHERE id_company = $company_id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}*/

	public function get_posts_for_company($company_id){
		$this->load->model("company_model");

		$sql = "SELECT * FROM mini_blog WHERE company_id = $company_id";

		$query = $this->db->query($sql);
		//error_log($sql);
		return $query->result_array();
	}


	public function get_posts_for_dropdown($company_id=FALSE){
		$level = $this->user_model->get_level();

		$query = 'SELECT id, title FROM mini_blog';
		$query2 = "SELECT id, title FROM mini_blog WHERE company_id = $company_id";

		if($company_id == FALSE){
			$query = $this->db->query($query);
		}
		else{
			$query = $this->db->query($query2);
		}
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[$row->id] = $row->title;
			}
			return $result;
		}
		else return null;
	}


	public function get_post_by_id($id=FALSE){
		if($id === FALSE){
			return;
		}

		$query = $this->db->get_where('mini_blog', array("id" => $id));
		return $query->row_array();
	}

	public function set_post(){
		$this->load->helper('url');
		$data = array(
			"title" => $this->input->post("title"),
			"message" => $this->input->post("message")
		);
		$result = $this->db->insert('mini_blog', $data);
		$last_id = $this->db->insert_id();
		if($result){
			return $last_id;
		}
		else{
			return false;
		}
	}


	public function update_post(){
		$this->load->helper('url');
		$id = $this->input->post('id');
		$data = array(
			'title' => $this->input->post('title'),
			'message' =>  $this->input->post('message')
		);

		error_log("Ska uppdatera med data " . print_r($data, true));
		$this->db->where('id', $id);
		$result = $this->db->update('mini_blog',$data);
		//error_log($this->db->last_query());
		return $result;

	}

	public function delete_post($id){
		$level = $this->user_model->get_level();
		if($level > 1){
			return;
		}
		$res = $this->db->delete('mini_blog', array('id' => $id));
		return $res;
	}

}
?>
