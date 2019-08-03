<?php
class Customer_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function get_customers($email=FALSE){

		if($email === FALSE){
			$query = $this->db->get('kund');
			return $query->result_array();
		}

		$query = $this->db->get_where('kund', array('email' => $email));
		return $query->row_array();
	}

	public function get_companys_customers($company_id){
		error_log("get_companys_customers");
		$sql = "SELECT kund.* FROM companies_kunder LEFT JOIN kund ON (id_kund = id) WHERE id_company = $company_id ORDER BY namn";
		error_log($sql);
		$query = $this->db->query($sql);

		return $query->result_array();

	}

	public function get_customers_with_company(){
		error_log("get_customers_with_company");
		$sql = "SELECT name as Company, namn as Kund, kund.id, password, kund.email as Kund_mail, tel1, tel2 FROM company_info ci LEFT JOIN companies_kunder ON (ci.id = id_company) LEFT JOIN kund ON (id_kund = kund.id)";
		error_log($sql);
		$query = $this->db->query($sql);

		return $query->result_array();

	}

	public function get_customers_and_nr_of_comp($order_by="namn"){
		$sql = "select distinct kund.*, (select count(*) from companies_kunder where id_kund = kund.id) as antal_anknytna_företag from kund left join companies_kunder on (kund.id = id_kund) left join company_info on (company_info.id = id_company) ORDER BY $order_by";

		$query = $this->db->query($sql);

		return $query->result_array();
	}

	public function get_customers_by_id($id=FALSE){
		if($id === FALSE){
			$query = $this->db->get('kund');
			return $query->result_array();
		}

		$query = $this->db->get_where('kund', array('id' => $id));
		return $query->row_array();
	}

	public function set_customer_simple(){
		$this->load->helper('url');
		$data = array(
			'namn' => $this->input->post('name'),
			'email' => $this->input->post('email'),
			'tel1' => $this->input->post('tel1'),
			'tel2' => $this->input->post('tel2'),
			'password' => '0000'
		);


		return $this->db->insert('kund', $data);
	}

	public function set_customer($return_id = FALSE)
	{
		error_log("set_customer");
		$this->load->helper('url');
		$data = array(
			'namn' => $this->input->post('name'),
			'email' => $this->input->post('email'),
			'tel1' => $this->input->post('tel1'),
			'tel2' => $this->input->post('tel2'),
			'password' => '0000'
		);
		error_log(print_r($data, true));


		$res1 = $this->db->insert('kund', $data);
		$insert_id = $this->db->insert_id();

		error_log("indert id: $insert_id");

		$level = $this->user_model->get_level();
		$data2 = array('id_kund' => $insert_id);//into link-table
		if($level == 1){
			$data2['id_company'] = $this->input->post('company_id');
		}
		else if($level == 2){
			error_log("Ska hämta agents_company");
			$company = $this->staff_model->get_agents_company_id();
			$data2['id_company'] = $company;
		}
		else{
			exit("wrong level" . $level);
		}
		error_log(print_r($data2, true));
		//länka kund med company
		$res2 = $this->db->insert('companies_kunder', $data2);
		if($return_id){
			return $insert_id;
		}
		return $res2;
	}

	public function update_customer()
	{
		$this->load->helper('url');
		$data = array(
			'id' => $this->input->post('id'),
			'namn' => $this->input->post('name'),
			'email' => $this->input->post('email'),
			'tel1' => $this->input->post('tel1'),
			'tel2' => $this->input->post('tel2')
		);
		return $this->db->replace('kund',$data);
	}

	public function delete_customer_record($id){
		$this->db->where('id_kund',$id);
		$this->db->delete('kunder_arbetsplatser');//ta bort kopplingar
		$this->db->where('id',$id);
		return $this->db->delete('kund');
	}

	public function get_customers_view1($id=FALSE){
		if($id === FALSE){
			$query = $this->db->query("SELECT id_kund id, id_arbetsplats, kund.namn as kund, arbetsplats.namn as arbetsplats FROM arbetsplats left join kunder_arbetsplatser on (arbetsplats.id = id_arbetsplats) left join kund on (id_kund = kund.id)");
			return $query->result_array();
		}

		$query = $this->db->query("SELECT id_kund id, id_arbetsplats FROM kunder_arbetsplatser WHERE id = '$id'");
		return $query->row_array();
	}

	public function get_related_workplaces($id=FALSE){
		if($id!==FALSE){
			$query = $this->db->query("select * from kunder_arbetsplatser join arbetsplats on (id_arbetsplats = id) WHERE id_kund = '$id'");
			return $query->result_array();
		}
	}

	public function get_unrelated_workplaces($id=FALSE){
		if($id!==FALSE){
			$query = $this->db->query("select * from kunder_arbetsplatser right join arbetsplats on (id_arbetsplats = id) where (id_kund != '$id' or isnull(id_kund))");
			return $query->result_array();
		}
	}

	//Anknyter en arbetsplats till en kund
	public function add_workplace(){
		$this->load->model('user_model');//för log i db
		echo "Inne i model - add_workplace";
		$this->user_model->write_log("Inne i model - add_workplace");
		$this->load->helper('url');
		$data = array(
			'id_kund' => $this->input->post('customer'),
			'id_arbetsplats' => $this->input->post('workplace')
		);
		error_log("Ska spara " . serialize($data));
		$result = $this->db->insert('kunder_arbetsplatser', $data);
		error_log("Result från query $result");
		return $result;
/*
		try{
			$this->db->trans_start(FALSE);
			$this->db->insert('kunder_arbetsplatser', $data);
			$this->db->trans_complete();
			$db_error = $this->db->error();
			if (!empty($db_error)) {
				throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
				error_log("Errors: " . print_r($db_error, true));
			}
		} catch(Exception $e){
			error_log('error: ' . $e->getMessage());
			return false;
		}
		return true;*/
	}
	
	public function add_company(){
		$this->load->model('user_model');
		$this->load->helper('url');
		$data = array(
			'id_kund' => $this->input->post('customer'),
			'id_company' => $this->input->post('company')
		);
		error_log("Ska spara " . serialize($data));
		$result = $this->db->insert('companies_kunder', $data);
		error_log("Result från query $result");
		return $result;

	}

	public function get_related_companies($id=FALSE){
		if($id!==FALSE){
			$query = $this->db->query("select * from companies_kunder join company_info on (id_company = id) WHERE id_kund = $id");
			return $query->result_array();
		}
	}

	public function get_unrelated_companies($id=FALSE){
		if($id!==FALSE){
			$query = $this->db->query("select * from companies_kunder join company_info on (id_company = id) where id_company not in (select id_company from companies_kunder where id_kund=$id)");
			return $query->result_array();
		}
	}
}
?>
