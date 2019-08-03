<?php
class Reports extends MY_Controller {

	private $css_string; 

	public function __construct()
	{
		error_log("Reports constructor");
		parent::__construct();
		$this->load->library('session');
		$this->load->model('reports_model');
		$this->load->model('job_model');
		$this->load->model('user_model');
		$this->load->helper('url_helper');
		$this->load->helper('screen_out_helper');
		$this->load->helper('date_check_helper');

		$company = $this->staff_model->get_agents_company();
		$this->company_name = $company["name"];
		$this->street = $company["gatuadress"];
		$this->postal_code = $company["postnummer"];
		$this->email_ = $company["email"];
		$this->phone = $company["telefon"];
		$this->cctld = $company["cctld"];

		date_default_timezone_set('Europe/Stockholm');//TODO: refer to company_info column cctld, for location

		$this->css_string =  "<link rel=\"stylesheet\" type=\"text/css\" href=\"".
			base_url()."css/roboto.css\"/><link rel=\"stylesheet\" type=\"text/css\" href=\"".
			base_url()."css/dhtmlxcalendar.css\" /><link rel=\"stylesheet\" type=\"text/css\" href=\"".
			base_url()."css/style.css\" />";

	}

	public function index($id=FALSE){
		/*Erase excel-file (from phpExcel) in same dir, for user*/
		$file_name = "./application/controllers/Reports_" . base64_decode($_SESSION["user_id"]) . ".xlsx";
		if(file_exists($file_name)){
			unlink($file_name);

		}

		$file_name = "./application/models/Reports_model_" . base64_decode($_SESSION["user_id"]) . ".xlsx";
		if(file_exists($file_name)){
			unlink($file_name);
			//error_log("Found and deleted $file_name");
		}


		$this->load->helper('form');//todo: encoda alla id i excel-länkar
		$this->user_model->write_log("Reports Index contr. id (GET): $id");

		//vid datum-avgränsning
		$from = $this->input->post('cal_1');
		$to = $this->input->post('cal_2');

		$person = $this->input->post("person");

		//för script
		$data['head_script'] = "var myCalendar; function doOnLoad() { myCalendar = new dhtmlXCalendarObject(['cal_1','cal_2']);	}";
		$data['head_ext_script'] = "<script src=\"".base_url()."js/dhtmlxcalendar.js\"></script>";
		$data['head_ext_css'] = $this->css_string;

		$this->user_model->write_log("I Reports index fr: $from, to $to, pers $person");

		$data['level'] = $level = $this->user_model->get_level();

		if(!isset($level) || $level<0 || $level>3){
			exit;
		}

		if(empty($from) && empty($to)){
		    error_log("from and to are empty, level är $level");
			if($level==1){

				if($id===FALSE && empty($person)){ //$person är om admin valt en person
					$data['reports'] = $this->reports_model->get_reports_view_3();
					$data['excel_link'] = "index.php/reports/make_excel/";
					$data['id'] = 0;
				}
				else{
					$id = ($id===FALSE) ? $person : $id;//either GET / POST
					$data['reports'] = $this->reports_model->get_reports_view_3($id);
					$data['id'] = $id;
					$data['excel_link'] = "index.php/reports/make_excel/".base64_encode($id);
				}
			}
			else if ($level == 2){
                if($id===FALSE && empty($person)) {
                    $data["id"] = base64_decode($_SESSION["user_id"]);
                    $data['reports'] = $this->reports_model->get_reports_for_company($this->staff_model->get_agents_company_id());
                }
                else{
                    $id = ($id===FALSE) ? $person : $id;//either GET / POST
                    $data['reports'] = $this->reports_model->get_reports_view_3($id);
                    $data['id'] = $id;
                    $data['excel_link'] = "index.php/reports/make_excel/".base64_encode($id);
                }
			}
			else if($level == 3){//hämtar för inloggad person
				$data["id"] = base64_decode($_SESSION["user_id"]);
				$data['reports'] = $this->reports_model->get_reports_view_3(base64_decode($_SESSION["user_id"]));
				$data['excel_link'] = "index.php/reports/make_excel/".$_SESSION["user_id"];
			}
		}
		else{//datum för begränsning
			//todo gör att även admin level 1 ska kunna se excel på sina egna rapporter
            error_log("from and to not empty");
			if($level==1){//hämtar all personals rapporter

				$data["id"] = $this->input->post("id");
				if(isset($data["id"])){
					$who = $data["id"];
				}
				else{
					$who = FALSE;
				} 
				$data['reports'] = $this->reports_model->get_reports_view_3_date($who,$from,$to);

				$data['excel_link'] = "index.php/reports/make_excel/NULL/NULL/$from/$to";
				$data["from"] = $from;
				$data["to"] = $to;
			}
			else{//hämtar egna rapporter
				$data["id"] = base64_decode($_SESSION["user_id"]);
				$data['reports'] = $this->reports_model->get_reports_view_3_date(base64_decode($_SESSION["user_id"]),$from,$to);
				$data['excel_link'] = "index.php/reports/make_excel/?id=".$_SESSION["user_id"]."&filename=''&from=$from&to=$to";//"index.php/reports/make_excel/".base64_decode($_SESSION["user_id"])."/NULL/$from/$to";
				$data["from"] = $from;
				$data["to"] = $to;
			}
		}

		$data['title'] = $this->company_name;


		$data["summa"] = $summa = 0;

		if(empty($data['reports']))
			$data["message"] = "Inga poster funna";
		else{
			$data["message"] = sizeof($data['reports']) . " poster funna";

			$count_sec = 0;
			foreach($data["reports"] as $report){
				$start = new DateTime($report["check_in_time"]);
				$end = new DateTime($report["check_out_time"]);
				$diff = $end->diff($start);

				$days = $diff->d;
				$hours = $diff->h;
				$minutes = $diff->i;
				$seconds = $diff->s;

				$ds = $days*24*60*60;//seconds
				$hs = $hours*60*60;
				$ms = $minutes*60;

				$tot_sec = $ds+$hs+$ms+$seconds;
				$count_sec+=$tot_sec;
			}
			$data["summa"] = $summa = $count_sec;
			$data["interval"] = $diff->format("%d dagar %h timmar %i minuter %s sekunder");
		}

		//echo $id;
		header_view_footer('report/index', $data);
	}

	public function choose_staff(){
		$this->load->helper('form');
		$data['level'] = $level = $this->user_model->get_level();

		if($level==1){

			$staff = $this->staff_model->get_staff();

		}
		else if($level == 2){
		    $company = $this->staff_model->get_agents_company_id();
		    $staff = $this->staff_model->get_staff_by_company($company);
        }
		else{
			exit("Ej access");
		}
        $data["staff"] = $staff;
        $data["title"] = "Välj personal";

        header_view_footer('report/choose', $data);
	}

	public function create()
	{
		$data['level'] = $level = $this->user_model->get_level();

		if($level<3){

			$this->user_model->write_log("Reports create contr.");
			$this->load->model('reports_model');
			$this->load->helper('form');
			$this->load->library('form_validation');

			$data['title'] = 'Skapa en rapport-post';
			$data['jobs'] = $this->job_model->get_jobs_view_1();
			$data['staff'] = $this->staff_model->get_staff();

			$data['head_script'] = "var myCalendar; function doOnLoad() { myCalendar = new dhtmlXCalendarObject(['cal_1','cal_2']);	}";
			$data['head_ext_script'] = "<script src=\"".base_url()."js/dhtmlxcalendar.js\"></script><script src=\"".base_url()."js/Script.js\"></script>";
			$data['head_ext_css'] = $this->css_string;

			$this->form_validation->set_rules('job', 'Jobb-id', 'trim');
			$this->form_validation->set_rules('person', 'Personal-id', 'trim');
			$this->form_validation->set_rules('check_in_time', 'Instämpel-tid', 'trim|required');
			$this->form_validation->set_rules('check_in_lati', 'Instämpel-latitud', 'trim|required');
			$this->form_validation->set_rules('check_in_longi', 'Instämpel-longitud', 'trim|required');
			$this->form_validation->set_rules('check_out_time', 'Utstämpel-tid', 'trim|required');
			$this->form_validation->set_rules('check_out_lati', 'Utstämpel-latitud', 'trim|required');
			$this->form_validation->set_rules('check_out_longi', 'Utstämpel-longitud', 'trim|required');

			if ($this->form_validation->run() === FALSE)
			{
				header_view_footer('report/create', $data);
			}
			else
			{
				$this->reports_model->set_report();
				header_view_footer('report/success');
			}
		}//if level < 3
		else{
			$data['message'] = "Kontakta ansvarig för denna sida för info";
			header_view_footer('pages/error', $data);
		}
	}


	public function edit($id){
		$this->user_model->write_log("Reports edit contr.");
		$id = base64_decode($id);
		$this->load->model('reports_model');
		$this->load->helper('form');
		$this->load->library('form_validation');

		$data['title'] = 'Redigera en rapport-post';
		$data['jobs'] = $this->job_model->get_jobs_view_1();
		$data['staff'] = $this->staff_model->get_staff();
		$report = $data['report'] = $this->reports_model->get_reports_view_2($id);
		if( is_null($report["arbetstillfalle_id"]) || $report["arbetstillfalle_id"] == 0){
			//om inget arbets-tillfälle är registrerat
			//skicka till edit_2
			redirect('/reports/edit_2/'. base64_encode($id));
		}

		$data['head_script'] = "var myCalendar; function doOnLoad() { myCalendar = new dhtmlXCalendarObject(['cal_1','cal_2']);	}";
		$data['head_ext_script'] = "<script src=\"".base_url()."js/dhtmlxcalendar.js\"></script><script type=\"text/javascript\" src=\"".base_url()."js/Script.js\"></script>";
		$data['head_ext_css'] = $this->css_string;
		$this->form_validation->set_rules('id', 'id', 'trim|required');
		$this->form_validation->set_rules('job', 'Jobb-id', 'trim');
		$this->form_validation->set_rules('person', 'Personal-id', 'trim');
		$this->form_validation->set_rules('check_in_time', 'Instämpel-tid', 'trim|required');
		$this->form_validation->set_rules('check_in_lati', 'Instämpel-latitud', 'trim|required');
		$this->form_validation->set_rules('check_in_longi', 'Instämpel-longitud', 'trim|required');
		$this->form_validation->set_rules('check_out_time', 'Utstämpel-tid', 'trim|required');
		$this->form_validation->set_rules('check_out_lati', 'Utstämpel-latitud', 'trim|required');
		$this->form_validation->set_rules('check_out_longi', 'Utstämpel-longitud', 'trim|required');

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('report/edit', $data);
		}
		else
		{
			$this->reports_model->set_report();
			header_view_footer('report/success');
		}
	}

	//för personal-rapport utan arbets-tillfälle angivet
	public function edit_2($id){
		$this->user_model->write_log("Reports edit_2 contr.");
		$id = base64_decode($id);
		$this->load->model('reports_model');
		$this->load->helper('form');
		$this->load->library('form_validation');

		$data['title'] = 'Redigera en rapport-post, utan arbets-tillfälle valbart';
		$data['staff'] = $this->staff_model->get_staff();
		$data['report'] = $this->reports_model->get_reports_by_id_view_3($id);
		$data['worked_minutes'] = $worked_minutes = $this->reports_model->get_work_length($id);

		$data['head_script'] = "var myCalendar; function doOnLoad() { myCalendar = new dhtmlXCalendarObject(['cal_1','cal_2']);	}";
		$data['head_ext_script'] = "<script src=\"".base_url()."/js/dhtmlxcalendar.js\"></script><script type=\"text/javascript\" src=\"".base_url()."js/Script.js\"></script>";
		$data['head_ext_css'] = $this->css_string;
		$this->form_validation->set_rules('id', 'id', 'trim|required');
		$this->form_validation->set_rules('person', 'Personal-id', 'trim');
		$this->form_validation->set_rules('check_in_time', 'Instämpel-tid', 'trim|required');
		$this->form_validation->set_rules('check_in_lati', 'Instämpel-latitud', 'trim|required');
		$this->form_validation->set_rules('check_in_longi', 'Instämpel-longitud', 'trim|required');
		$this->form_validation->set_rules('check_out_time', 'Utstämpel-tid', 'trim|required');
		$this->form_validation->set_rules('check_out_lati', 'Utstämpel-latitud', 'trim|required');
		$this->form_validation->set_rules('check_out_longi', 'Utstämpel-longitud', 'trim|required');

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('report/edit_2', $data);
		}
		else
		{
			$this->reports_model->set_report();
			header_view_footer('report/success');
		}

	}

	public function update(){
		$this->user_model->write_log("Reports update contr.");
		error_log("update");
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Uppdatera en jobb-rapports uppgifter';
		
		$form_data = $this->input->post();
		error_log(print_r($form_data, true));
		$data['report'] = $this->reports_model->get_reports($form_data["id"]);
		$this->form_validation->set_rules('id', 'id', 'trim|required');
		$this->form_validation->set_rules('job', 'Jobb-id', 'trim');
		$this->form_validation->set_rules('person', 'Personal-id', 'trim|required');
		$this->form_validation->set_rules('check_in_time', 'Instämpel-tid', 'trim|required');
		$this->form_validation->set_rules('check_in_lati', 'Instämpel-latitud', 'trim');
		$this->form_validation->set_rules('check_in_longi', 'Instämpel-longitud', 'trim');
		$this->form_validation->set_rules('check_out_time', 'Utstämpel-tid', 'trim|required');
		$this->form_validation->set_rules('check_out_lati', 'Utstämpel-latitud', 'trim');
		$this->form_validation->set_rules('check_out_longi', 'Utstämpel-longitud', 'trim');
		$this->form_validation->set_rules('rast_m', 'Rast i minuter', 'trim|required|callback_is_int_and_positive_or_zero');
		$this->form_validation->set_rules('benamning', 'Benämning', 'trim');	
		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('report/edit', $data);
		}
		else
		{	
			//TODO: check level
			$this->reports_model->update_report();
			header_view_footer('report/success');
		}
	}

	public function check(){
		error_log("controller check");
		$this->user_model->write_log("Reports check contr.");
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->model('user_model');

		$data['title'] = 'Stämpla in/ut';
		$this->user_model->write_log("check...");
		$data['user_id'] = $_SESSION["user_id"];
		$person = $this->staff_model->get_staff_by_id(base64_decode($_SESSION["user_id"]));
		//$this->user_model->write_log(implode(", ", $person));
		$data['full_name'] = $person["fornamn"]." ".$person["efternamn"];
		$data["lat_name"] = "";
		$data["lon_name"] = "";
		$data["head_ext_script"]= "<script src=\"".base_url()."js/Script.js\"></script>"; //<script src=\"".base_url()."/js/scripts.js\"></script>
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";

		$unfinished_report = $this->reports_model->get_unfinished_report($person['id']);//om finns - en (array)

		$check_type = 0;

		if(empty($unfinished_report)){
			$this->user_model->write_log("ingen påbörjad rapport");
			$check_type = $data['check_type'] = "in";
		}
		else{
			$this->user_model->write_log("påbörjad rapport");
			$check_type = $data['check_type'] = 'out';
		}

		$data["lat_name"] = "check_".$check_type."_lat";
		$data["lon_name"] = "check_".$check_type."_lon";

		$form_data = $this->input->post();

		$this->form_validation->set_rules('check_type','Stämpel-typ','trim|required');//in eller ut

		//$check_type = $form_data["check_type"];
		if($check_type == "in"){
			//$this->form_validation->set_rules('check_time', 'Instämpel-tid', 'trim|required');
			$this->form_validation->set_rules('check_in_lat', 'Instämpel-latitud', 'trim|required|min_length[5]|callback_valid_lat');
			$this->form_validation->set_rules('check_in_lon', 'Instämpel-longitud', 'trim|required|min_length[5]|callback_valid_long');
		}
		else if($check_type == "out"){
			//då finns en lagrad påbörjad rapport
			//$this->user_model->write_log(print_r($unfinished_report, true));
			if(!empty($unfinished_report["benamning"])){
				//$this->user_model->write_log("Benämning ej tom");
				$data["benamning"] = $unfinished_report["benamning"];
			}
			else{
				//$this->user_model->write_log("Benämning tom");
			}
			$data['report'] = $unfinished_report;//$this->reports_model->get_reports(base64_decode($_SESSION["user_id"]));
			//$this->form_validation->set_rules('check_time', 'Utstämpel-tid', 'trim|required');
			$this->form_validation->set_rules('check_out_lat', 'Utstämpel-latitud', 'trim|required|min_length[5]|callback_valid_lat');
			$this->form_validation->set_rules('check_out_lon', 'Utstämpel-longitud', 'trim|required|min_length[5]|callback_valid_long');         
		}
		else{
			$this->user_model->write_log("fel check typ");
			exit();
		}
		//Om formulär ej skickats
		//Man står vid check-ut
		//Todo: skicka med benamning om det finns
		if ($this->form_validation->run() === FALSE){
			$this->user_model->write_log("form validation -> run är FALSE (?..)");
			header_view_footer('report/check', $data);
		}
		else{
			$check_time = $form_data["check_time"];
			if(!validateDate($check_time)){
				exit("Fel på datum");
			}
			$this->user_model->write_log("Datum är godkänt");

			if($check_type == "in"){
				$success = $this->reports_model->check_in();
				if($success){
					$data["message"] = "Du stämplade in med datum $check_time";
					header_view_footer('report/check_success', $data);
				}
				else{
					header_view_footer('report/check_failure');
				}
			}
			else if($check_type == "out"){/*TODO reqest break info*/
				//echo "Hittade check type out";
				//$this->user_model->write_log("check_type == 'out', ska anropa reports_model check_out");
				$report_id = $this->reports_model->check_out();
				//$this->reports_model->check_out();
				if($report_id){//TODO: visa check_time
					redirect('/reports/request_break_info/'. $report_id);
				}
				else{
					header_view_footer('report/check_failure');
				}
			}
			else{
				echo "Fel på check-typ<br>";
			}
		}
	}

	public function check_no_coords(){
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$data['title'] = 'Stämpla in eller ut';
		$this->user_model->write_log("check_no_coords...");
		$user_id = $data['user_id'] = $_SESSION["user_id"];
		$this->user_model->write_log("user id: " . $data['user_id']);
		$person = $this->staff_model->get_staff_by_id(base64_decode($_SESSION["user_id"]));
		$this->user_model->write_log(implode(", ", $person));
		$data['full_name'] = $person["fornamn"]." ".$person["efternamn"];
		$data["head_ext_script"]= "<script src=\"".base_url()."js/Script.js\"></script>"; //<script src=\"".base_url()."/js/scripts.js\"></script>
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		$unfinished_report = $this->reports_model->get_unfinished_report($person['id']);//om finns - en
		$check_type = 0;

		if(empty($unfinished_report)){
			$this->user_model->write_log("ingen påbörjad rapport");
			$check_type = $data['check_type'] = "in";
		}
		else{
			$this->user_model->write_log("påbörjad rapport");
			$check_type = $data['check_type'] = 'out';
		}
		$form_data = $this->input->post();
		$this->form_validation->set_rules('check_type','Stämpel-typ','trim|required');
		if($check_type == "in"){
			//$this->form_validation->set_rules('check_time', 'Instämpel-tid', 'trim|required');
		}
		else if($check_type == "out"){
			//då finns en lagrad påbörjad rapport
			$data['report'] = $unfinished_report;//$this->reports_model->get_reports(base64_decode($_SESSION["user_id"]));
			//$this->form_validation->set_rules('check_time', 'Utstämpel-tid', 'trim|required');
		}
		else{
			$this->user_model->write_log("fel check typ");
			exit();
		}
		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('report/check', $data);
		}
		else
		{
			$check_time = $form_data["check_time"];
			if(!validateDate($check_time)){
				exit();
			}
			$this->user_model->write_log("Datum är godkänt");

			if($check_type == "in"){
				$this->reports_model->check_in_no_coords();
				header_view_footer('report/check_success');
			}
			else if($check_type == "out"){
				//echo "Hittade check type out";
				$report_id = $this->reports_model->check_out_no_coords();//TODO: add break duration
				//$this->load->view('report/check_success');
				$this->user_model->write_log("Efter utstämpling... report id: $report_id");
				if($report_id){
					$this->user_model->write_log("Ska göra redirect till /reports/request_break_info/" . $report_id);
					redirect('/reports/request_break_info/'. $report_id);
				}
				else{
					//check-out failed... nothing written
					//TODO: send message about this to user
					//$this->load->view('templates/header', $data);
					header_view_footer('report/check_failure');
					//$this->load->view('templates/footer');

				}
			}
			else{
				echo "Fel på check-typ<br>";
			}
		}
	}

	//This is for a page comming after the checking out, asking for break in minutes
	public function request_break_info($report_id){/*TODO skicka max rast möjlig (arbetstid i minuter) */
		$this->user_model->write_log("request_break_info");
		/*$id = $this->uri->segment(3);*/
		$this->load->helper('form');//?
		$this->load->library('form_validation');
		$data['title'] = 'Vänligen ange rast';
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		$data['user_id'] = $_SESSION["user_id"];
		$person = $this->staff_model->get_staff_by_id(base64_decode($_SESSION["user_id"]));
		$data['full_name'] = $person["fornamn"]." ".$person["efternamn"];
		$this->user_model->write_log("user id: " . $data['user_id']);
		$data['report_id'] = $report_id;
		$data['worked_minutes'] = $worked_minutes = $this->reports_model->get_work_length($report_id);
		header_view_footer('report/request_break_info', $data);

	}
	//To add any brake in minutes
	public function add_break_info(){
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->user_model->write_log("contr add_break_info...");
		$data['title'] = 'Vänligen ange rast';
		$user_id = $data['user_id'] = $_SESSION["user_id"];
		$person = $this->staff_model->get_staff_by_id(base64_decode($_SESSION["user_id"]));
		$data['full_name'] = $person["fornamn"]." ".$person["efternamn"];
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		$form_data = $this->input->post();
		$report_id = $data["report_id"] = $form_data["report_id"];
		$this->form_validation->set_rules('break_time','Rastens tid, minuter','trim|required|callback_is_int_and_positive_or_zero');
		$data['worked_minutes'] = $worked_minutes = $this->reports_model->get_work_length($report_id);

		if ($this->form_validation->run() === FALSE){
			$this->user_model->write_log("form ej godkänt");
			header_view_footer('report/request_break_info', $data);
		}
		else{
			$break_time = $form_data["break_time"];
			$this->user_model->write_log("Form godkänt, report_id: $report_id, break_time: $break_time");
			if($this->reports_model->add_break_info($report_id, $break_time)){
				header_view_footer('report/check_success');
			}
		}
	}

	public function is_int_and_positive_or_zero($number_str){
		if($number_str == "0"){
			return true;
		}
		else if(!is_numeric($number_str)){
			return false;
		}
		$val = intval($number_str);
		if($val > 0){
			return true;
		}
		//else
		$this->form_validation->set_message('break_time', '{field} måste vara minst 0');
		return false;
	}

	//not used 181204
	public function undo_checkout($report_id){
		/*
		 * $data = array('name' => $name, 'email' => $email, 'url' => $url);
		 * $where = "author_id = 1 AND status = 'active'";
		 * $str = $this->db->update_string('table_name', $data, $where);*/

		/*$data = array('check_out_time' => 'NULL', 'longi_out' => 'NULL', 'lati_out' => 'NULL', 'benamning' => 'NULL');
		$where = "id = $report_id";
		$sql = $this->db->update_string('arbets_rapport', $data, $where);
		if($this->db->query($sql)){
			$this->load->view('report/check_success');
		}
		else{
			$this->load->view('report/check_failure');
		}*/
		if($this->reports_model->check_out_undo()){
			header_view_footer('report/check_success');
		}
		else{
			header_view_footer('report/check_failure');
		}
	}


	public function make_excel_post(){
		error_log("Controller, make_excel_post");
		$this->load->helper('download');
		$this->load->helper('form');

		$form_data = $this->input->post();

		error_log(print_r($form_data, true));

		$id = $form_data["idp"];
		$from = $form_data["from"];
		$to = $form_data["to"];

		error_log("id fr. form_data: " . $id);


		$filename = "Jobbrapport.xlsx";//Todo: kolla om används

		if(empty($id)){
			error_log("id id empty");
			$id=FALSE;
		}

		if(empty($from)){
			$from = FALSE;
		}
		if(empty($to)){
			$to = FALSE;
		}

		if($id!==FALSE){
			$id = intval(base64_decode($id));
			if($id==0){//Varför använda?
				error_log("id==0");
				$id=FALSE;
			}
			error_log("Fick id $id");
		}

		//echo "<h1>Gör excel-fil</h1>";
		$this->user_model->write_log("Nu i make_excel_post med id $id, filename $filename, from $from, to $to");
		if($from === FALSE && $to === FALSE){
			if($id===FALSE){//||$id===NULL||$id==NULL||$id=="NULL"
				$this->user_model->write_log("Ska hämta data utan id eller datumgräns");        
				$data["reports"] = $this->reports_model->get_reports_view_3();//ladda alla rapporter
			}
			else{
				$data["reports"] = $this->reports_model->get_reports_view_3($id);//en viss persons rapporter
			}
		}
		else{ //avgränsning av data
			$this->user_model->write_log("ska hämta rapporter med datumavränsning");
			$this->user_model->write_log("id är $id");
			if($id===FALSE){//||$id===NULL||$id==="NULL"
				$this->user_model->write_log("-ska anropa get_reports_view_3_date med NULL, $from, $to");
				$data["reports"] = $this->reports_model->get_reports_view_3_date(0,$from,$to);//ladda alla rapporter
			}
			else{//ej testat
				$this->user_model->write_log("ska anropa get_reports_view_3_date med $id $from $to");
				$data["reports"] = $this->reports_model->get_reports_view_3_date($id,$from,$to);//en viss persons rapporter
			}
		}

		$data["title"] = "Skapa Excel-fil för all personals rapporter";

		if(sizeof($data['reports'])==0){
			exit("<br>Ingen data alls<br>");
		}
		else{
			//echo "<p>Rader: " . sizeof($data['reports']) . "</p>";
		}

		if($from===FALSE||is_null($from)||$from==="NULL"){
			$from = FALSE;
		}
		if($to===FALSE||is_null($to)||$to==="NULL"){
			$to = FALSE;
		}
		$today = date("Y-m-d");

		$data["message"] = "Skapar fil...";
		if($id===FALSE){
			if($from===FALSE && $to===FALSE){
				$this->user_model->write_log("aaa");
				$objPHPExcel = $data["objPHPExcel"] = $this->reports_model->make_excel();
			}
			elseif($to===FALSE){
				$this->user_model->write_log("Arbetsrapport fr.o.m. $from");
				$objPHPExcel = $data["objPHPExcel"] = $this->reports_model->make_excel('',$from,$today,"Arbetsrapport från $from");
			}
			elseif($from===FALSE){
				$this->user_model->write_log("Arbetsrapport t.o.m. $to");
				$objPHPExcel = $data["objPHPExcel"] = $this->reports_model->make_excel('','1975-01-01',$to, "Arbetsrapport till $to");
			}
			else{
				$this->user_model->write_log("Arbetsrapport för $from - $to");
				$objPHPExcel = $data["objPHPExcel"] = $this->reports_model->make_excel('',$from,$to,"$from - $to");
			}

		}
		else{
			if($from===FALSE && $to===FALSE){
				$this->user_model->write_log("baa");
				$objPHPExcel = $data["objPHPExcel"] = $this->reports_model->make_excel($id);
			}
			elseif($to===FALSE){
				$this->user_model->write_log("bab");
				$objPHPExcel = $data["objPHPExcel"] = $this->reports_model->make_excel($id,$from,$today,"Arbetsrapport fr.o.m. $from");
			}
			elseif($from===FALSE){
				$this->user_model->write_log("bba");
				$objPHPExcel = $data["objPHPExcel"] = $this->reports_model->make_excel($id,'1975-01-01',$to, "Arbetsrapport t.o.m. $to");
			}
			else{
				$this->user_model->write_log("bbb");
				$objPHPExcel = $data["objPHPExcel"] = $this->reports_model->make_excel($id,$from,$to,"Arbetsrapport $from - $to");
			}
		}


		if (PHP_SAPI == 'cli'){
			die('This example should only be run from a Web Browser');
		}

		/** Include PHPExcel */
		require_once './PHPExcel/Classes/PHPExcel.php';

		//spara till servern

		//so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		error_log("Har satt aktivt ark");


		// Save Excel 2007 file
		//echo date('H:i:s') , " Write to Excel2007 format" , EOL;
		$callStartTime = microtime(true);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		error_log("Har skapat writer");

		$name_end = '_' . base64_decode($_SESSION["user_id"]) . '.xlsx';
		$objWriter->save(str_replace('.php', $name_end, __FILE__));//todo: gör filnamn med datum
		
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;
		$callStartTime = microtime(true);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;

		$name = str_replace('.php', $name_end, pathinfo(__FILE__, PATHINFO_BASENAME));//Excel 2007

		$data["filename"] = $filename = base_url() . "application/controllers/" . $name; //för att kunna hämta filen
		error_log("Filnamn: $filename");

		if (ob_get_contents()) ob_end_clean();
		error_log("Ska anropa file_get_contents med " . str_replace('.php', $name_end, __FILE__));
		$data = file_get_contents(str_replace('.php', $name_end, __FILE__)); //assuming my file is on localhost
		$name = 'Rapport.xlsx';//Filnamn till användare - på samma variabel
		error_log("Ska köra force_download med $name");
		force_download($name,$data);

	}

	function make_pdf_post(){

		error_log("Controller, make_pdf_post");
		$this->load->helper('download');
		$this->load->helper('form');

		$form_data = $this->input->post();

		error_log(print_r($form_data, true));

		$id = $form_data["idp"];
		$from = $form_data["from"];
		$to = $form_data["to"];

		error_log("id fr. form_data: " . $id);


		$filename = "Jobbrapport.xlsx";//Todo: kolla om används

		if(empty($id)){
			error_log("id id empty");
			$id=FALSE;
		}

		if(empty($from)){
			$from = FALSE;
		}
		if(empty($to)){
			$to = FALSE;
		}

		if($id!==FALSE){
			$id = intval(base64_decode($id));
			if($id==0){//Varför använda?
				error_log("id==0");
				$id=FALSE;
			}
			error_log("Fick id $id");
		}

		//echo "<h1>Gör excel-fil</h1>";
		$this->user_model->write_log("Nu i make_pdf_post med id $id, filename $filename, from $from, to $to");
		if($from === FALSE && $to === FALSE){
			if($id===FALSE){//||$id===NULL||$id==NULL||$id=="NULL"
				$this->user_model->write_log("Ska hämta data utan id eller datumgräns");        
				$data["reports"] = $this->reports_model->get_reports_view_3();//ladda alla rapporter
			}
			else{
				$data["reports"] = $this->reports_model->get_reports_view_3($id);//en viss persons rapporter
			}
		}
		else{ //avgränsning av data
			$this->user_model->write_log("ska hämta rapporter med datumavgränsning");
			$this->user_model->write_log("id är $id");
			if($id===FALSE){//||$id===NULL||$id==="NULL"
				$this->user_model->write_log("-ska anropa get_reports_view_3_date med NULL, $from, $to");
				$data["reports"] = $this->reports_model->get_reports_view_3_date(0,$from,$to);//ladda alla rapporter
			}
			else{//ej testat
				$this->user_model->write_log("ska anropa get_reports_view_3_date med $id $from $to");
				$data["reports"] = $this->reports_model->get_reports_view_3_date($id,$from,$to);//en viss persons rapporter
			}
		}

		$data["title"] = "Skapa Pdf-fil för personals rapporter";

		if(sizeof($data['reports'])==0){
			exit("<br>Ingen data alls<br>");
		}
		else{
			//echo "<p>Rader: " . sizeof($data['reports']) . "</p>";
		}

		if($from===FALSE||is_null($from)||$from==="NULL"){
			$from = FALSE;
		}
		if($to===FALSE||is_null($to)||$to==="NULL"){
			$to = FALSE;
		}
		$today = date("Y-m-d");

		$data["message"] = "Skapar fil...";
		if($id===FALSE){
			if($from===FALSE && $to===FALSE){
				$this->user_model->write_log("aaa");
				$filename_pdf = $data["objPHPExcel"] = $this->reports_model->make_pdf();
			}
			elseif($to===FALSE){
				$this->user_model->write_log("Arbetsrapport fr.o.m. $from");
				$filename_pdf = $data["objPHPExcel"] = $this->reports_model->make_pdf('',$from,$today,"Arbetsrapport från $from");
			}
			elseif($from===FALSE){
				$this->user_model->write_log("Arbetsrapport t.o.m. $to");
				$filename_pdf = $data["objPHPExcel"] = $this->reports_model->make_pdf('','1975-01-01',$to, "Arbetsrapport till $to");
			}
			else{
				$this->user_model->write_log("Arbetsrapport för $from - $to");
				$filename_pdf = $data["objPHPExcel"] = $this->reports_model->make_pdf('',$from,$to,"$from - $to");
			}

		}
		else{
			if($from===FALSE && $to===FALSE){
				$this->user_model->write_log("baa");
				$filename_pdf = $data["objPHPExcel"] = $this->reports_model->make_pdf($id);
			}
			elseif($to===FALSE){
				$this->user_model->write_log("bab");
				$filename_pdf = $data["objPHPExcel"] = $this->reports_model->make_pdf($id,$from,$today,"Arbetsrapport fr.o.m. $from");
			}
			elseif($from===FALSE){
				$this->user_model->write_log("bba");
				$filename_pdf = $data["objPHPExcel"] = $this->reports_model->make_pdf($id,'1975-01-01',$to, "Arbetsrapport t.o.m. $to");
			}
			else{
				$this->user_model->write_log("bbb");
				$filename_pdf = $data["objPHPExcel"] = $this->reports_model->make_pdf($id,$from,$to,"Arbetsrapport $from - $to");
			}
		}

		error_log("Har fått filnamn $filename_pdf");
		if($filename_pdf){
			if (ob_get_contents()) ob_end_clean();
			error_log("Ska anropa file_get_contents med $filename_pdf, true");
			$data = file_get_contents($filename_pdf, true);
			$name = 'Rapport_' . date("Y-m-d") . ".pdf";//Filnamn till användare
			error_log("Ska köra force_download med $name");
			force_download($name,$data);

		}
		else{
			echo "Fel, kunde inte skapa pdf";
		}
	}

	public function delete_row($id){
		if(empty($_SESSION["user_name"])){
			redirect('/');
		}

		if($this->user_model->get_level() > 1){
			redirect('/');
		}
		$data['title'] = 'Delete a report record';

		if(is_numeric($id)){
			if($this->reports_model->delete_report($id)){
				header_view_footer('report/success');
			}
			else{
				echo "Gick inte";
			}
		}
	}

	public function getLevel(){
		if(isset($_SESSION["user_name"])){
			return $this->user_model->get_level();
		}
	}

	public function check_filter($time,$lat,$long){

	}

	public function valid_lat($val){
		$this->user_model->write_log("valid_lat med $val");
		return ($val < 80 && $val > -80);
	}

	public function valid_long($val){
		return($val < 180 && $val > -180);
	}

	/*API*/
	public function getLastReport($code){
		error_log("getLastReport");
		//test
		$this->load->model('reports_model');
		$user_id = $this->staff_model->staff_by_code($code);

		if($user_id){
			error_log("Found user");
			//echo info json
			$row = $this->reports_model->get_users_last_report($user_id);
			$json = json_encode($row);
			error_log("Got\n$json");
			echo json_encode($row);
		}
		else{
			echo "error";
		}
	}
}
