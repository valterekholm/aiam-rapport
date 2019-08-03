<?php
class Company extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('customer_model');
		$this->load->model('user_model');//för log i db
		$this->load->model('company_model');
		//$this->load->helper('url_helper');
		//$this->load->helper('form_helper');
		$this->load->helper('screen_out_helper');
		$this->load->helper(array('form', 'url'));
	}

	public function index()
	{
		$title = "";
		if($this->user_model->get_level() > 1){
			$company = $this->staff_model->get_staffmembers_company($_SESSION["user_name"]);
			$title = "Info om ditt företag";
			error_log("Got id " . $company["id"]);
			$data["id"] = $company["id"];
		}

		else if($this->user_model->get_level() == 1){
			if(null !== $this->input->post('company')){
				error_log($this->input->post('company'));
				$company_id = $this->input->post('company');
				$company = $this->company_model->get_company_by_id($company_id);
				$title = "Info om företag";
				error_log("Got id $company_id");
				$data["id"] = $company_id;
			}
			else{
				redirect('company/superadmin');//det saknas val av företag
			}
		}
		else{
			header_view_footer('pages/error');	
		}


		$data['name'] = $company["name"];
		$data['street'] = $company["gatuadress"];
		$data['postal_code'] = $company["postnummer"];
		$data['email'] = $company["email"];
		$data['phone'] = $company["telefon"];
		$data['cctld'] = $company["cctld"];
		$data['controlled_mode'] = $this->company_model->get_controlled_mode($data["id"]);
		//$data['log_save_days'] = $company1["log_save_days"];
		$data['title'] = $title;
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		error_log("company/index controlled_mode: " . $data['controlled_mode']);
		header_view_footer('company/index', $data);
		//$this->load->view('company/index');
		//$this->load->view('templates/footer');
	}

	public function superadmin(){

		$this->load->model('company_model');

		if($this->user_model->get_level() > 1){
			$this->load->view('pages/error');
		}

		$data["head_ext_css"] = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>' . "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />\n";
		$data["head_ext_script"] = '<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==" crossorigin=""></script>';

		$data["head_css"] = "#map1 { height: 180px; }";

		$data['title'] = "Admin alla företag";
		$data["options"] = $this->company_model->get_companies_for_dropdown();
		error_log("superadmin med 'options' " . print_r($data["options"], true));
		header_view_footer('company/superadmin', $data);
		//$this->load->view('company/superadmin');
		//$this->load->view('templates/footer');

	}
    
    public function create()
    {
	    $this->load->helper('form');
	    $this->load->library('form_validation');

	    $data['title'] = 'Spara info om kund';
	    $this->form_validation->set_rules('name', 'Namn', 'trim|required');
	    $this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email');
	    $this->form_validation->set_rules('phone', 'Telefon', 'trim');
	    $this->form_validation->set_rules('street', 'Gatuadress', 'trim');
	    $this->form_validation->set_rules('postcode', 'Postnummer', 'trim|min_length[5]|max_length[6]');
	    $this->form_validation->set_rules('land_code', 'Landskod', 'trim|min_length[2]|max_length[2]');/*https://www.w3schools.com/tags/ref_language_codes.asp*/

	    if ($this->form_validation->run() === FALSE)
	    {
		    header_view_footer('company/create', $data);
		    //$this->load->view('company/create');
		    //$this->load->view('templates/footer');
	    }
	    else
	    {
		    if($this->company_model->set_company())
			    header_view_footer('company/success');
		    else{
			    header_view_footer('pages/error');
		    }
	    }
    }
/*
	public function edit($id){
		$id = base64_decode($id);
		if(!is_numeric($id)){
			$data['heading'] = 'Det fattas uppgifter';
			$data['message'] = 'Inget kan därför göras.';
			$this->load->view('templates/header', $data);
			$this->load->view('errors/html/error_general');
			$this->load->view('templates/footer');
		}
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Redigera kunds uppgifter';
		$data['customer'] = $this->customer_model->get_customers_by_id($id);
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";

		$data['related_workplaces'] = $this->customer_model->get_related_workplaces($id);
		$data['sub_header'] = (count($data['related_workplaces'])>0?"Anknytna arbetsplatser":"Har ingen arbetsplats");
		$this->load->view('templates/header', $data);
		$this->load->view('customer/edit');
		$this->load->view('templates/footer');
	}
 */
	public function update(){
		$this->user_model->write_log("I Company update");
		$this->load->model('company_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Uppdatera en kunds uppgifter';

		$form_data = $this->input->post();
		$data["reuse"] = $form_data;
		//todo: implementera email fält
		$this->form_validation->set_rules('id', 'Id', 'trim|required');
		$this->form_validation->set_rules('name','Namn','trim|required');
		//$this->form_validation->set_rules('street', 'Gatuadress', 'trim|required');
		//$this->form_validation->set_rules('postal_code', 'Postnummer', 'trim|required');
		$this->form_validation->set_rules('phone', 'Telefon', 'trim');
		$this->form_validation->set_rules('cctld', 'Cctld internet-landskod', 'trim');
		$this->form_validation->set_rules('email', 'Epost', 'trim');

		if ($this->form_validation->run() == FALSE)
		{
			header_view_footer('company/index', $data);
			//$this->load->view('company/index');
			//$this->load->view('templates/footer');
		}
		else
		{
			if( $this->company_model->update_company() ){
				header_view_footer('company/success');
			}
			else{
				$data["message"] = "Fel vid query";
				header_view_footer('pages/error', $data);
			}
		}
	}

	public function delete_customer_relation($id_company,$id_customer, $view){
		$company = base64_decode($id_company);
		$customer = base64_decode($id_customer);
		if($view === "co"){
			$view = 'company/success';
		}
		else if($view === "cu"){
			$view = 'customer/success';
		}
		else{
			$view = '/';
		}
		$data['title'] = 'Delete a company-customer association';

		if(is_numeric($company) && is_numeric($customer)){
			if($this->company_model->delete_connection_company_customer($company,$customer)){
				header_view_footer($view);
			}
			else{
				echo "Gick inte";
			}
		}

		else{
			echo "Saknas rätt input";
		}

	}

	public function add_customer($id_company, $id_customer, $view){
		$company = base64_decode($id_company);
		$customer = base64_decode($id_customer);
		if($view === "co"){
			$view = 'company/success';
		}
		else if($view === "cu"){
			$view = 'customer/success';
		}
		else{
			$view = '/';
		}
		$data['title'] = 'Skapa en association, företag-kund';
		if(is_numeric($company) && is_numeric($customer)){

		}

	}
}

?>
