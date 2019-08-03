<?php
class Jobschema extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('customer_model');
		$this->load->model('user_model');//för log i db
		$this->load->model('company_model');
$this->load->model('job_model');
		$this->load->model('schema_model');
		$this->load->helper('screen_out_helper');
		$this->load->helper(array('form', 'url'));
	}

	public function index()
	{
		$title = "Jobb-scheman";
		$level = $this->user_model->get_level();

		if($level == 3){
			$schemas = $this->schema_model->get_schemas_for_staffmember(base64_decode($_SESSION["user_id"]));
		}

		else if($level == 2){
			$schemas = $this->schema_model->get_schemas_for_company($this->staff_model->get_agents_company_id());
			$data["id"] = $this->staff_model->get_agents_company_id();
		}

		else if($level == 1){
			
			$schemas = $this->schema_model->get_schemas();
		}
		else{
			header_view_footer('pages/error');
			return;
		}


		$data['schemas'] = $schemas;
		$data['title'] = $title;
		//$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";
		header_view_footer('jobschema/index', $data);
		//$this->load->view('company/index');
		//$this->load->view('templates/footer');
	}

	public function superadmin(){
	}

	public function listing($date=FALSE, $code=FALSE, $repetitions=10){

		if($date == false){
			$date = date("Y-m-d H:i");
		}
		if($code == false){
			$code = "d";
		}

		if(is_a($date, 'DateTime')){
			$data["startdate"] = $date->format("Y-m-d H:i");
		}
		else if(is_string($date)){
			$data["startdate"] = $date;
		}
		else{
			return;
		}
		$data["title"] = "Lista av schema";
		$data["code"] = $code;
		$data["repetitions"] = $repetitions;
		header_view_footer('jobschema/listing', $data);
	}
    
	public function create()
	{
		$this->load->library('form_validation');

		$level = $this->user_model->get_level();

		if($level > 2){
			$data["message"] = "Endast högre admin har behörighet.";
			header_view_footer('pages/error', $data);
			return;
		}

		$data['title'] = 'Spara nytt schema';
		$data["chain_table"] = get_table_chain_staff("jobb_schema");
		$data["jobs"] = $this->job_model->get_jobs_for_dropdown();
		$this->form_validation->set_rules('job', 'Arbetstillfälle', 'trim|required');
		//$this->form_validation->set_rules('schemacode', 'Schema-kod', 'trim|required');

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('jobschema/create', $data);
		}
		else
		{
			if($this->schema_model->set_schema())
				header_view_footer('jobschema/success');
			else{
				header_view_footer('pages/error');
			}
		}
	}

	public function add_to_job()
	{
		error_log("add_to_job");
		$this->load->library('form_validation');
		$level = $this->user_model->get_level();

		if($level > 2){
			$data["message"] = "Endast högre admin har behörighet.";
			header_view_footer('pages/error', $data);
			return;
		}
		else if($level<1){
			header_view_footer('pages/error');
			return;
		}
		$data['title'] = "Lägga schema till arbetstillfälle";
		$this->form_validation->set_rules('job', 'Arbetstillfälle', 'trim|required');
		$this->form_validation->set_rules('startDate', 'Utgångs-datum', 'trim|required');
		if ($this->form_validation->run() === FALSE)
		{
			error_log("Form validated false");
			header_view_footer('job/add_schema', $data);
		}
		else{
			error_log("Form validation passed");
			$job = $this->input->post('job');
			$weekDays = $this->input->post('weekDays');
			$startD = $this->input->post("startDate");
			$di = $this->input->post('di');
			$wi = $this->input->post('wi');
			$mi = $this->input->post('mi');
			/*code*/
			$code = isset($weekDays) ? $weekDays : "";
			$code .= (isset($di) && $di>0) ? "d".$di : "";
			$code .= (isset($wi) && $wi>0) ? "w".$wi : "";
			$code .= (isset($mi) && $mi>0) ? "m".$mi : "";
			error_log("After parsing: code is $code");
			$code_id = $this->schema_model->get_id_schema_saved_or_existing($code);
			error_log("Got id of code $code: $code_id");
			$job_updated = $this->job_model->set_job_field("schema_id", $code_id, $job);
			if(!$job_updated){
				$data["message"] = "Kunde ej uppdatera arb.tillfälle";
				header_view_footer('pages/error', $data);
				return;
			}
			error_log("Uppdaterade arb.tillf");

			/*$start_date, $schema_code, $limit_start, $limit_end*/
			$end_test_date = new DateTime($startD);
			$end_test_date->modify("+1 month");
			$endD = $end_test_date->format("Y-m-d");
			$events = $this->schema_model->generate_events_from_jobschema($startD, $code, $startD, $endD);

			error_log("Got events in add_to_job: " . count($events));
			$data["dates"] = $events;
			$b64 = base64_encode($job);
			$edit_again_link = anchor("jobs/edit/$b64", "åter till jobbet");
			$data["message"] = "Arbetstillfälle fick schema, gå $edit_again_link?";
			header_view_footer('jobschema/success', $data);

		}

	}

	public function edit($id){
		$id = base64_decode($id);
		if(!is_numeric($id)){
			$data['heading'] = 'Det fattas uppgifter';
			$data['message'] = 'Inget kan därför göras.';
			header_view_footer('errors/html/error_general', $data);
		}
		$level = $this->user_model->get_level();
		if($level < 1 || $level > 2){
			return;
		}
		$this->load->library('form_validation');
		$data['title'] = 'Redigera schema';
		$data['schema'] = $this->schema_model->get_schema_by_id($id);
		//$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";

		header_view_footer('jobschema/edit', $data);
	}

	public function update(){
		$this->load->library('form_validation');
		$data['title'] = 'Uppdatera ett schema';

		$form_data = $this->input->post();
		$data["reuse"] = $form_data;
		//todo: implementera email fält
		$this->form_validation->set_rules('schemacode','Schema-kod','trim|required');

		if ($this->form_validation->run() == FALSE)
		{
			header_view_footer('jobschema/index', $data);
		}
		else
		{
			if( $this->schema_model->update_schema() ){
				header_view_footer('jobschema/success');
			}
			else{
				$data["message"] = "Fel vid query";
				header_view_footer('pages/error', $data);
			}
		}
	}
}

?>
