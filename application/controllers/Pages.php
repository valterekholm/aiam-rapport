<?php
class Pages extends CI_Controller {

        /*public function view($page = 'home')
        {
        }*/
        public function __construct()
        {
                parent::__construct();
                $this->load->library('session');
                $this->load->model('user_model');
		        $this->load->model('reports_model');//för in-/utcheck
		        $this->load->model('miniblog_model');
                $this->load->helper('url_helper');

                $company = $this->staff_model->get_agents_company();
                $this->company_name = $company["name"];
                $this->street = $company["gatuadress"];
                $this->postal_code = $company["postnummer"];
                $this->email_ = $company["email"];
                $this->phone = $company["telefon"];
                $this->cctld = $company["cctld"];
        }

	/*Startsida*/
        public function view($page = 'home')
        {
            $this->load->model("user_model");
            $data["company_name"] = $this->company_name;
            $data["level"] = $this->user_model->get_level();
            //$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";
            $data['head_ext_script'] = "<script src=\"".base_url()."js/Script.js\"></script>";

            //$this->user_model->write_log("I Pages > view med page = $page");

        if ( ! file_exists(APPPATH.'views/pages/'.$page.'.php'))
        {
                // Whoops, we don't have a page for that!
                show_404();
        }

	$data['title'] = ucfirst("hem");//$page); // Capitalize the first letter

	if($this->miniblog_model->any_admin_post()){
		$data['last_blog'] = $this->miniblog_model->get_latest_post();
	}
	else{
		$data['last_blog'] = null;
	}

        $this->load->view('templates/header', $data);
        $this->load->view('pages/'.$page, $data);
        $this->load->view('templates/footer', $data);
        }
}
