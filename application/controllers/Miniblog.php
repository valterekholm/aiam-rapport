<?php
class Miniblog extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('miniblog_model');
		$this->load->model('user_model');//för log i db
		$this->load->model('company_model');
$this->load->model('job_model');
		$this->load->model('schema_model');
		$this->load->helper('screen_out_helper');
		$this->load->helper(array('form', 'url'));
	}

	public function index()
	{
		$title = "Mini-blog";
		$level = $this->user_model->get_level();

		if($level == 3){
			//$blogs = $this->miniblog_model->get_posts_for_staffmember(base64_decode($_SESSION["user_id"]));
		}

		else if($level == 2){
			//$blogs = $this->miniblog_model->get_posts_for_company($this->staff_model->get_agents_company_id());
			//$data["id"] = $this->staff_model->get_agents_company_id();
		}

		else if($level == 1){
			
			$posts = $this->miniblog_model->get_posts();
		}
		else{
			header_view_footer('pages/error');
			return;
		}


		$data['posts'] = $posts;
		$data['title'] = $title;
		//$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";
		header_view_footer('miniblog/index', $data);
		//$this->load->view('company/index');
		//$this->load->view('templates/footer');
	}

	public function superadmin(){
	}

	public function listing($from_date=FALSE){

		if($from_date == false){
			$from_date = "1980-09-01";
		}

		if(is_string($from_date)){
		}
		$data["title"] = "";
		header_view_footer('miniblog/listing', $data);
	}
    
	public function create()
	{
		$this->load->library('form_validation');

		$level = $this->user_model->get_level();

		if($level > 1){
			$data["message"] = "Endast högre admin har behörighet.";
			header_view_footer('pages/error', $data);
			return;
		}

		$data['title'] = 'Spara nytt inlägg';
		$this->form_validation->set_rules('title', 'Rubrik', 'trim|required');
		$this->form_validation->set_rules('message', 'Meddelande', 'trim|required');

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('miniblog/create', $data);
		}
		else
		{
			if($this->miniblog_model->set_post())
				header_view_footer('miniblog/success');
			else{
				header_view_footer('pages/error');
			}
		}
	}

	public function edit($id){
		$id = base64_decode($id);
		if(!is_numeric($id)){
			$data['heading'] = 'Det fattas uppgifter';
			$data['message'] = 'Inget kan därför göras.';
			header_view_footer('errors/html/error_general', $data);
			return;
		}
		$level = $this->user_model->get_level();
		if($level != 1){
			return;
		}
		$this->load->library('form_validation');
		$data['title'] = 'Redigera post';
		$data['post'] = $this->miniblog_model->get_post_by_id($id);
		//$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";

		header_view_footer('miniblog/edit', $data);
	}

	public function update(){
		$this->load->library('form_validation');
		$data['title'] = 'Uppdatera ett inlägg';

		$form_data = $this->input->post();
		$data["reuse"] = $form_data;
		//todo: implementera email fält
		$this->form_validation->set_rules('title','Rubrik','trim|required');
		$this->form_validation->set_rules('message', 'Meddelande', 'trim|required');

		if ($this->form_validation->run() == FALSE)
		{
			header_view_footer('miniblog/index', $data);
		}
		else
		{
			if( $this->miniblog_model->update_post() ){
				header_view_footer('miniblog/success');
			}
			else{
				$data["message"] = "Fel vid query";
				header_view_footer('pages/error', $data);
			}
		}
	}
}

?>
