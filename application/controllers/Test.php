<?php
class Test extends CI_controller {

        public function __construct()
        {
                parent::__construct();
				$this->load->library('session');
				$this->load->model('user_model');
				$this->load->helper('url_helper');
				$this->load->helper('screen_out_helper');
				$this->load->helper('random_helper');
				
				$company = $this->staff_model->get_agents_company();
				$this->company_name = $company["name"];
				$this->street = $company["gatuadress"];
				$this->postal_code = $company["postnummer"];
				$this->email_ = $company["email"];
				$this->phone = $company["telefon"];
				$this->cctld = $company["cctld"];
				
				$this->user_model->write_log("Test constructor");
		}

		public function index()
		{
			
			$this->load->helper('form');
            $this->load->library('form_validation');
			
			$level = 0;
			if(empty($_SESSION["user_name"])){
				redirect('/');
			}
			else{
				$level = $this->user_model->get_level();
			}

			//$data['staff'] = $level == 1 || $level == 2 ? $this->staff_model->get_staff() : $this->staff_model->get_staff_by_id(base64_decode($_SESSION["user_id"]));
			$data['title'] = 'Test, ' . $this->company_name;
			$data['serv'] = $this->input->server('SERVER_NAME');//?
			//$data['head_ext_css'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"../../../../admin/style.css\" />";
			$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";

			$this->load->view('templates/header', $data);
			$this->load->view('test/index', $data);
			$this->load->view('templates/footer');
			
			$this->user_model->write_log("Test->index");
	}
}
