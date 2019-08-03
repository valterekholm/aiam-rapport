<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->library('session');

	}

	public function get_first_company(){
		$query = $this->db->query('SELECT * FROM company_info LIMIT 1');
		return $query->row_array();
	}

	public function get_companies_for_dropdown(){
		$query = $this->db->query('SELECT id, name FROM company_info ORDER BY name');
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[$row->id] = $row->name;
			}
			return $result;
		}
		else return null;
	}

	public function get_company_by_id($company_id){
		error_log("get_company_by_id");
		$query = $this->db->get_where('company_info', array('id' => $company_id));
		return $query->row_array();
	}

	/* returns 1 or 0 */
	public function get_controlled_mode($company_id){
		if($company_id == null){
			return false;
		}
		$sql = "SELECT CAST(controlled_mode AS unsigned) controlled_mode from company_info WHERE id = $company_id";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		error_log("Found controlled mode: " . $row["controlled_mode"]);
		return $row["controlled_mode"];
	}

	public function get_company_data($id=FALSE){
		error_log("get_company_data");
		//$this->load->model('staff_model'); //8 jun 19, incl by config

		if($id == FALSE){
			$company = $this->staff_model->get_agents_company();
		}
		else{
			$company = $this->get_company_by_id($id);
		}

		$comp = array();

		if($company != null){
			error_log(print_r($company, true));
		}


		$comp["controlled_mode"] = $this->get_controlled_mode($this->staff_model->get_agents_company_id());
		//Förut satte "controlled_mode" till false om agent var super-admin

		return $company;
	}

	public function get_timezone($comp_id){
		$sql = "SELECT time_zone FROM company_info WHERE id = $comp_id";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		return $row["time_zone"];
	}

	public function set_company(){
		$this->load->helper('url');
		$name = $this->input->post('name');
		$street = $this->input->post('street');
		$postal_code = $this->input->post('post_code');
		$phone = $this->input->post('phone');
		$cctld = $this->input->post('land_code');
		$email = $this->input->post('email');
		$cm = $this->input->post('controlled_mode');

		if(isset($cm)){
			$cm = 1;
		}
		else{
			$cm = 0;
		}

		$data = array('name'=>$name, 'gatuadress'=>$street, 'postnummer'=>$postal_code, 'telefon'=>$phone, 'email'=>$email, 'cctld'=>$cctld, 'controlled_mode'=>$cm);

		$res = $this->db->insert('company_info', $data);

		return $res;


	}

	public function update_company(){
		$this->load->helper('url');
		$name = $this->input->post('name');
		$street = $this->input->post('street');
		$postal_code = $this->input->post('postal_code');
		$phone = $this->input->post('phone');
		$cctld = $this->input->post('cctld');
		$email = $this->input->post('email');
		$id = $this->input->post('id');
		$controlled_mode = (null !== $this->input->post('controlled_mode') ? 1 : 0);

		$query = $this->db->query("UPDATE company_info SET name = '$name', gatuadress='$street', postnummer='$postal_code', telefon='$phone', email='$email', cctld='$cctld', controlled_mode = $controlled_mode WHERE id = $id");
		return $query; // TRUE or FALSE
	}

	public function delete_connection_company_customer($id_company, $id_customer){
		error_log("delete_connection_company_customer($id_company, $id_customer)");
		$array = array('id_company'=>$id_company, 'id_kund'=>$id_customer);
		$this->db->where($array);
		return $this->db->delete('companies_kunder');

	}

	public function make_connection_company_customer($id_company, $id_customer){
		$array = array('id_company'=>$id_company, 'id_kund'=>$id_customer);
		return $this->db->insert('companies_kunder', $array);
	}

	public function get_related_staff_ids_string($company_id){
		$sql = "SELECT p.id FROM personal p WHERE company_id = $company_id";
		$query = $this->db->query($sql);
		$ids = implode($query->result_array());
		error_log("get_related_staff_ids_string, got $ids");
		return $ids;
	}

}
