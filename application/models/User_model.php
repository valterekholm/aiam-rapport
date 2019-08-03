<?php
class User_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function login(){
		//echo "usermodel/login";
		$this->load->model('reports_model');
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		error_log("login med $email och ***** ");
	    /*$data = array(
	    'email' => $this->input->post('email'),
	    'password' => $this->input->post('password')
	    );*/ //5 aug -17
	    $query = $this->db->get_where('personal', array('email' => $email));
		foreach($query->result() as $row){
			if(password_verify($password, $row->password)){
				$_SESSION["user_name"] = $email;
				$_SESSION["user_id"] = base64_encode($row->id);

				if($this->reports_model->user_has_unfinished_report()){
					$open_report = $this->reports_model->get_unfinished_report($row->id);
					$_SESSION["open_report"] = $open_report["check_in_time"];
				}
				else{
					$_SESSION["open_report"] = "";
				}

				return ($_SESSION["user_name"] == $email);
			}
			else{

			}
		}
		echo "Inlogg ej godkänd<br>";

	}

	public function logout()
	{
		//todo: nollställ level
		//
		error_log("logout user " . $this->session->user_id);//$_SESSION["user_id"]);
		$_SESSION["user_id"] = 0;//test, för det loggades ej ut i Opera 58.0.3135.79
		session_unset();
		session_destroy();
		session_write_close();
		setcookie(session_name(), '', 0, '/');
		session_start();//test
		session_regenerate_id(true);//ger warning - session is not active //todo: kolla om behövs
	}

	//requires the calling code to have $this->load->library('session');
	public function get_level(){
		//$this->load->model('staff_model'); //8 jun 19, include by config
		if(isset($_SESSION["user_id"])){
			//echo "get_level för ".base64_decode($_SESSION["user_id"])."<br>";
			$person = $this->staff_model->get_staff_by_id(base64_decode($_SESSION["user_id"]));
			//var_dump($person);
			return $person["level"];
		}
		else{
			error_log("Session 'user_id' saknas");
			return 0;
		}
	}



	public function update_user()
	{
	}

	public function delete_user_record($id){

	}

	public function get_users_view1($id=FALSE){
		if($id === FALSE){

		}
	}




	public function add_user(){
		$this->load->helper('url');
		$data = array(
			'id_kund' => $this->input->post('customer'),
			'id_arbetsplats' => $this->input->post('workplace')
		);
		return $this->db->insert('kunder_arbetsplatser', $data);
	}

	public function write_log($message){
		$data = array(
			"date" => date("Y-m-d H:i:s"),
			"message" => $message);

		return $this->db->insert('log', $data);
	}

	public function clear_old_log(){
		$query = $this->db->query("select * from company_info");
		$row = $query->row();

		$span = $row->log_save_days;
		$today = date("Y-m-d");

		$old_day = strtotime($today . ' -0 years -0 months ' . $span . ' days');

		$query = $this->db->query("delete from log where date < '$old_day'");
	}
}
?>
