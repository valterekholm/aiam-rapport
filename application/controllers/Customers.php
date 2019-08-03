<?php
class Customers extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('customer_model');
		$this->load->model('user_model');//för log i db
		$this->load->model('company_model');
		$this->load->helper('url_helper');
		$this->load->helper('screen_out_helper');

		if($this->user_model->get_level() > 1){
			$this->controlled_mode = $this->company_model->get_controlled_mode($this->staff_model->get_agents_company_id());
		}
		else{
			$this->controlled_mode = false;
		}
	}

	public function index($order_by="namn")
	{
		//todo: customers for company
		$level = $this->user_model->get_level();
		error_log("level $level");
		switch($level){
		case 1:
			error_log("Ska hämta ALLA kunder");
			$data['customers'] = $this->customer_model->get_customers_and_nr_of_comp($order_by);//get_customers();
			break;
		case 2:
			$company_id = $this->staff_model->get_staffmembers_company_id(base64_decode($_SESSION["user_id"]));
			error_log("Ska kolla efter kunder till company $company_id");
			$data['customers'] = $this->customer_model->get_companys_customers($company_id);
			break;
		}	
		$data['title'] = 'Alla kunder';
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		header_view_footer('customer/index', $data);
	}

	public function create()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		$data['title'] = 'Spara info om kund';
		$data['level'] = $level = $this->user_model->get_level();
		$this->form_validation->set_rules('name', 'Namn', 'trim|required');
		$this->form_validation->set_rules('email', 'E-mail', 'trim|valid_email');//required
		$this->form_validation->set_rules('tel1', 'Telefon', 'trim');
		$this->form_validation->set_rules('tel2', 'Telefon alternativ', 'trim');

		$data["chain_table"] = get_table_chain("kund");
		if($level==1){
			$this->form_validation->set_rules('company_id', 'Company_id', 'trim|required');

		}

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('customer/create', $data);
		}
		else
		{
			$this->customer_model->set_customer();
			header_view_footer('customer/success');
		}
	}

	/*for internal use - must be logged in*/
	public function create_ajax($name=FALSE, $email=FALSE, $tel1=FALSE, $tel2=FALSE){
		header("Access-Control-Allow-Origin: ". base_url());
		if($email){
			$email = urldecode($email);
		}
		error_log("create_ajax med data: $name , $email , $tel1 , $tel2");

		//med POST
		$level = $this->user_model->get_level();
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Namn', 'trim|required');
		$this->form_validation->set_rules('email', 'E-mail', 'trim|valid_email');//required
		$this->form_validation->set_rules('tel1', 'Telefon', 'trim');
		$this->form_validation->set_rules('tel2', 'Telefon alternativ', 'trim');
		if($level==1){ exit; } //behöver company id 'id_company'


		/*spara ny kund*/

		if ($this->form_validation->run() === FALSE)
		{//missl
			echo "Validering ej passerad";
		}
		else
		{
			$new_id = $this->customer_model->set_customer(true);//reuse POST, true=get new id
			//header_view_footer('customer/success');
			/*Förenkla: skicka till baka json med id och namn (till select)*/
			$result = array();
			$result[] = array("id" => $new_id, "name" => trim($this->input->post("name")));
			echo json_encode($result);
		}
		/*returnera id*/
	}

	public function edit($id){
		$id = base64_decode($id);
		if(!is_numeric($id)){
			$data['heading'] = 'Det fattas uppgifter';
			$data['message'] = 'Inget kan därför göras.';
			header_view_footer('errors/html/error_general', $data);
		}
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Redigera kunds uppgifter';
		$data['customer'] = $this->customer_model->get_customers_by_id($id);
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";

		$data['related_workplaces'] = $this->customer_model->get_related_workplaces($id);
		//för superadmin
		if($this->user_model->get_level()==1){
			$data['related_companies'] = $this->customer_model->get_related_companies($id);
		}
		$data['sub_header'] = (count($data['related_workplaces'])>0?"Anknytna arbetsplatser":"Har ingen arbetsplats");
		header_view_footer('customer/edit', $data);
	}

	public function update(){
		$this->user_model->write_log("I Customers update");
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Uppdatera en kunds uppgifter';
		//test
		$form_data = $this->input->post();
		if(isset($id)){//?
			$data['customer'] = $this->customer_model->get_customers_by_id($id);
		}
		else{
			$data['customer'] = $this->customer_model->get_customers_by_id($form_data["id"]);
		}
		$this->form_validation->set_rules('id','Id','required');
		$this->form_validation->set_rules('name', 'Namn', 'trim|required');
		$this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email');
		$this->form_validation->set_rules('tel1', 'Telefon', 'trim');
		$this->form_validation->set_rules('tel2', 'Telefon alternativ', 'trim');

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('customer/edit', $data);
		}
		else
		{
			$this->customer_model->update_customer();
			header_view_footer('customer/success');
		}
	}

	public function delete_row($id){
		$data['title'] = 'Delete a customer record';

		if(is_numeric($id)){
			if($this->customer_model->delete_customer_record($id)){
				header_view_footer('customer/success');
			}
			else{
				echo "Gick inte";
			}
		}
	}

	public function connect_any_workplace($id_customer){
		$this->user_model->write_log("Inne i connect_any_workplace med kund id $id_customer");
		$id_customer = base64_decode($id_customer);
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Lägg till en arbetsplats till en kund';
		if(is_numeric($id_customer)){
			$this->user_model->write_log("ID är nummer");
			$data['customer'] = $this->customer_model->get_customers_by_id($id_customer);
			//print_r($data);
			$data['header'] = 'Arbetsplats för ' . $data["customer"]["namn"];

			$data['unrelated_workplaces'] = $this->customer_model->get_unrelated_workplaces($id_customer);

			header_view_footer('customer/edit_workplaces', $data);
		}
	}

	public function connect_any_company($id_customer){
		//för admin 1
		error_log("connect_any_company $id_customer");
		$level = $this->user_model->get_level();

		if($level != 1){
			error_log("fel level");
			exit();
		}
		$customer = base64_decode($id_customer);
		error_log($customer);
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Lägg till ett företag till en kund';
		if(is_numeric($customer)){
			$data['customer'] = $this->customer_model->get_customers_by_id($customer);
			$data['header'] = 'Företag för ' . $data["customer"]["namn"];
			$data['unrelated_companies'] = $this->customer_model->get_unrelated_companies($customer);
			header_view_footer('customer/edit_companies', $data);
		}
	}

	public function add_workplace(){
		$this->user_model->write_log("Inne i add_workplace");
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Koppla arbetsplats till kund';
		$data['header'] = "Koppla arbetsplats till kund";
		/*$data['workplace'] = $this->workplace_model->get_workplaces($form_data["id"]);*/
		$this->form_validation->set_rules('customer', 'Kund id', 'trim|required');
		$this->form_validation->set_rules('workplace', 'Arbetsplats id', 'trim|required');

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('customer/edit_workplaces', $data);
		}
		else
		{
			if($this->customer_model->add_workplace()){
				header_view_footer('customer/success');
			}
			else{
				/*if($this->db->_error_number() == 1062){
					$message = "Arbetsplats redan lagd till kund";
			}*/
				$message = "";
				header_view_footer('pages/error', $message);
			}
		}
	}

	public function add_company(){
		error_log("add_company");
		if($this->user_model->get_level()!=1) exit();

		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Koppla företag till kund';
		$data['header'] = "Koppla företag till kund";
		/*$data['workplace'] = $this->workplace_model->get_workplaces($form_data["id"]);*/
		$this->form_validation->set_rules('customer', 'Kund id', 'trim|required');
		$this->form_validation->set_rules('company', 'Företag id', 'trim|required');

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('customer/edit_companies', $data);
		}
		else
		{
			if($this->customer_model->add_company()){
				header_view_footer('customer/success');
			}
			else{
				/*if($this->db->_error_number() == 1062){
					$message = "Arbetsplats redan lagd till kund";
			}*/
				$message = "";
				header_view_footer('pages/error', $message);
			}
		}
	}

	public function view1(){
		$data['title'] = 'Se kunder med anknutna arbetsplatser';
		$data['customers'] = $this->customer_model->get_customers_view1();
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		//$data['head_ext_css'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"../../../../admin/style.css\" />";
		header_view_footer('customer/index', $data);
	}

	public function get_bl_customers(){//todo: anropa bl server api
		//ClientId: ed4d82fc-b6d7-4518-a292-abad6eadb9fb
		//ClientSecret: d8a11026-4a8c-4650-b159-32c791e7a1b9

		$post_data = array(
			"grant_type"=>"client_credentials",
			"scope"=>"",
			"client_id"=>"ed4d82fc-b6d7-4518-a292-abad6eadb9fb",
			"client_secret"=>"d8a11026-4a8c-4650-b159-32c791e7a1b9"
		);

		$this->call_bl_api_auth($post_data);
		//$result = CallAPI("POST","https://apigateway.blinfo.se/auth/oauth/v2/token")
	}



	public function call_bl_api_auth($post_data){
		$ch = curl_init('https://apigateway.blinfo.se/auth/oauth/v2/token');

		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => array(
				//'Authorization: '.$authToken,
				'Content-Type: application/x-www-form-urlencoded'
			),
			CURLOPT_POSTFIELDS => json_encode($post_data)
		));

		// Send the request
		$response = curl_exec($ch);

		// Check for errors
		if($response === FALSE){
			die(curl_error($ch));
		}

		// Decode the response
		$responseData = json_decode($response, TRUE);

		// Print the date from the response
		echo serialize($responseData);
	}

	/*for ajax internal*/
	/*returns array of arrays/objects? */
	public function get_customers_for_dropdown($company){
		header("Access-Control-Allow-Origin: ". base_url());
		error_log("get_customers_for_dropdown($company)");

		$cid = intval($company);
		if($cid>0){
			$cs = $this->customer_model->get_companys_customers($cid);
		}
		else if($cid == 0){
			$cs = $this->customer_model->get_customers();
		}
		else{
			echo "0";
			return false;
		}

		$data = array();
		foreach($cs as $c){
			$id = $c["id"];
			$name = $c["namn"];
			$data[] = array($id => $name);
		}
		error_log(print_r($data, true));
		echo json_encode($data);
	}

	/*for ajax internal*/

	public function get_form_create(){
		header("Access-Control-Allow-Origin: ". base_url());
		error_log("get_form_create");
		$url = site_url("customers/create_ajax");
		$html = "<form action='".site_url("customers/create_ajax/")."' method='post' onsubmit='formPostAjax(this); return false'>".
			"<div><label>Namn:</label>".
			"<input name='name'></div>".
			"<div><label>E-post:</label>".
			"<input name='email'></div>".
			"<div><label>Telefon:</label>".
			"<input name='tel1'></div>".
			"<div><label>Alternativ telefon:</label>".
			"<input name='tel2'></div>".
			"<div><input type='submit' value='Spara'>".
			"</form>";
		error_log("Ska eka: " . $html);
		echo $html;
		return;
	}
}
?>
