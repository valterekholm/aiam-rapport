<?php
class Users extends CI_Controller {//för session

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('user_model');
		$this->load->helper('url_helper');
		$this->load->helper('screen_out_helper');

		$company = $this->staff_model->get_agents_company();
		$this->company_name = $company["name"];
		$this->street = $company["gatuadress"];
		$this->postal_code = $company["postnummer"];
		$this->email_ = $company["email"];
		$this->phone = $company["telefon"];
		$this->cctld = $company["cctld"];

	}

	public function index()
	{
		//$data['staff'] = $this->users_model->get_users();
		$data['title'] = $company_name;//test
		//$data['serv'] = $this->input->server('SERVER_NAME');//?
		//$data['head_ext_css'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"../../../../admin/style.css\" />";

		header_view_footer('pages/home', $data);
	}

	public function login(){
		$this->load->helper('form');
		$this->load->library('form_validation');

		$data['title'] = 'Log in';
		$this->form_validation->set_rules('email', 'Epost', 'trim|required');
		$this->form_validation->set_rules('password', 'Lösenord', 'trim|required');

		if ($this->form_validation->run() === FALSE)
		{
			//$this->load->view('templates/header', $data);
			//$this->load->view('pages/home');
			//$this->load->view('templates/footer');
			redirect('pages/view');
		}
		else
		{
			//echo "<br>Godkänt formulär";
			if($this->user_model->login()){//POST data is re-used there
				$data["message"] = "Du loggade in";
				//$this->load->view('user/success', $data);
				header_view_footer('user/success', $data);
			}
			else{
				redirect('pages/view');/*
					$this->load->view('templates/header', $data);
				$this->load->view('pages/view');
				$this->load->view('templates/footer');*/
			}
		}

		//$username = preg_replace('/[^a-z0-9\-]/i', '_', $username);
		//$password = preg_replace('/[^a-z0-9\-]/i', '_', $password);

	}

	public function logout(){
		do{
			$this->user_model->logout();
			usleep(100);
		} while(isset($_SESSION["user_name"]));

		$data["message"] = "Har loggat ut";

		header_view_footer('user/success', $data);
	}


	public function create()
	{
	}


	public function edit($id){

	}

	public function update(){
	}
	public function delete_row($id){
	}

	public function getLevel(){
		if(isset($_SESSION["user_name"])){
			return $this->user_model->get_level();
		}
	}

	public function write_log($message){
		$this->user_model->write_log($message);

	}
}
