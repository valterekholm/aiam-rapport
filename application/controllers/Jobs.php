<?php
class Jobs extends MY_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('job_model');
		$this->load->model('user_model');
		$this->load->helper('url_helper');
		$this->load->helper('screen_out_helper');
		//$this->load->helper('mail_helper');

		$company = $this->staff_model->get_agents_company();
		$this->company_name = $company["name"];
		$this->street = $company["gatuadress"];
		$this->postal_code = $company["postnummer"];
		$this->email_ = $company["email"];
		$this->phone = $company["telefon"];
		$this->cctld = $company["cctld"];


	}//todo: visa anknyten personal vid edit
	public function index()
	{
		redirect("jobs/view1");
		$data['jobs'] = $this->job_model->get_jobs();
		$data["level"] = $level = $this->user_model->get_level();
		$data['title'] = 'Alla arbetstillfällen / jobb ' . $this->company_name;
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";
		if($level == 1){
			$data['jobs'] = $this->job_model->get_jobs();
		}
		else{
			$data['jobs'] = $this->job_model->get_jobs_for_company(
				$this->staff_model->get_agents_company_id()
			);
		}
		header_view_footer('job/index', $data);
	}

	public function view($id=FALSE, $obfuscated = FALSE){

		/*TODO: visa jobb repetition, om finns*/
		if($id==FALSE){
			$data["message"] = "Inget arbetstillfälle specificerat";
			header_view_footer('pages/error', $data);
			return;
		}
		$id = base64_decode($id);

		if($obfuscated == "yes"){
			$id = $id -4;
			$id = $id /3;
		}


		$data["level"] = $this->user_model->get_level();

		$job_view = $this->job_model->get_jobs_view_3($id);
		//print_r($job_view);
		$data["datum_start"] = $job_view["datum_start"];
		$data["datum_slut"] = $job_view["datum_slut"];
		$data["arbetsplats"] = $job_view["namn"];
		$data["gatu_adress"] = $job_view["gatu_adress"];
		$data["trappor"] = $job_view["trappor"];
		$data["postnummer"] = $job_view["postnummer"];
		$data["beskrivning"] = $job_view["beskrivning"];
		$data["latitud"] = $job_view["lati"];
		$data["longitud"] = $job_view["longi"];

		$data["related_staff"] = $this->job_model->get_staff($id);

		$data["title"] = "Info om arbetstillfälle";
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		$data["head_ext_css"] .=  '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>';
		$data['head_ext_script'] = '<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==" crossorigin=""></script>';


		header_view_footer('job/view', $data);
	}

	//sammanställd tabell
	public function view1()
	{
		//$this->user_model->write_log("Kör view1 i controller Jobs");
		$level = $this->user_model->get_level();
		if($level == 1){
			$data['jobs'] = $this->job_model->get_jobs_view_2_has_schema();

			$data['title'] = 'Alla arbetstillfällen';
		}
		else if($level == 2){
			$data['jobs'] = $this->job_model->get_jobs_for_comp($this->staff_model->get_agents_company_id());

			$data['title'] = 'Alla arbetstillfällen, ' . $this->company_name;
		}
		else if($level == 3){
			$data['jobs'] = $this->job_model->get_staffmembers_jobs(base64_decode($_SESSION["user_id"]));

			$data['title'] = 'Alla mina arbetstillfällen';
		}
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		$data["level"] = $this->user_model->get_level();
		header_view_footer('job/index', $data);
	}

	public function view1_personal()//inloggad persons jobb
	{
		$user_id = base64_decode($_SESSION["user_id"]);
		$this->user_model->write_log("Kör view1 i controller Jobs");
		$data['jobs'] = $this->staff_model->get_jobs_for_person($user_id);
		$data['title'] = 'Mina arbetstillfällen';
		$data["level"] = $this->user_model->get_level();
		header_view_footer('job/index', $data);
	}

	/*for internal calls*/
	public function get_staff_json($job=FALSE){
		header("Access-Control-Allow-Origin: ". base_url() . "");
		error_log("get_staff_json($job)");

		$staff_arr = array();


		/*$this->load->helper('form');
		$this->load->library('form_validation');*/

		$message = "";
		if($job == FALSE){

			$message = array("message" => "Argument saknas");
		}
		else{
			//using simple obfuscating
			$job = (intval($job)-4)/3;
			$staff = $this->job_model->get_staff_public($job);
			//error_log(print_r($staff, true));

			foreach ($staff as $person){
				$s = rtrim(json_encode($staff), "]");
				$s = ltrim($s, "[");

				//$p = array("fname"=>$person["fornamn"]);
				array_push($staff_arr, $person);
				

			}
			echo json_encode($staff_arr);
			exit();
		}


		echo json_encode($message);


	}


	//argument: day / week / month
	public function calendar($calendar_type = "week", $start=FALSE){
		$this->load->helper('calendar_helper');
		$this->load->library('calendarevent');
		//$this->load->library('monthprint');//used to inject in calendar for printCal
		//$this->load->library('weekprint');//used to inject in calendar for printCal
		//$this->load->library('dayprint');//used to inject in calendar for printCal
		//$this->load->library('calendar'/*, $params*/);//obs: $params = array('type' => 'large', 'color' => 'red');

		$printer = "";


		//kolla access level
		//1,2 se alla i kalender
		//3 se egna jobb bara
		//1 kunna ta bort / edit
		//2 kunna edit till viss del
		$user_id = base64_decode($_SESSION["user_id"]);
		error_log("Controller: Jobs -> calendar, user $user_id, calendar_type $calendar_type");
		$data["title"] = "Kalender över arbetstillfällen";
		$data["level"] = $level = $this->user_model->get_level();
		//$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";
		$data['head_ext_script'] = "<script src=\"".base_url()."js/calendar.js\"></script><script src=\"".base_url()."js/Script.js\"></script>";



		$data["from_date"] = new DateTime();//used for print_calendar

		$from_date;//first day in cal
		$to_date;//last day in cal
		$today = new DateTime();
		$day_in_week = intval(date("N"));//1-7
		$day_in_week--;//gör till 0-baserat
		$day_in_month = intval(date("d"));
		$day_in_month--;//gör till 0-baserat
		//primär datumbegränsning utifrån kalender-typ
		switch($calendar_type){
		case "day":
			if(! $start){
			$from_date = date("Y-m-d");
			$to_date = date("Y-m-d");
			}
			break;
		case "week":
			//todo: ta bort (day_in_week - 1) dagar från idag = $from_date
			//TODO: make navigation work

			$this->load->library('weekprint');/*A class for printing a calendar*/
			$printer = $this->weekprint;
			$this->weekprint->logHello();

			if(! $start){
				if($day_in_week>0){
					$span_string = "P".$day_in_week."D";
					$from_date = clone $today;
					$from_date->sub(new DateInterval($span_string));
					error_log("week, starting $day_in_week earlier");
				}
				else{
					error_log("week, starting today");
					$from_date = $today;
				}
				$to_date = clone $from_date;
				$to_date->add(new DateInterval("P7D"));
			}
			else{
				//get DateTime
				$from_date = new DateTime($start);
				error_log("Calendar has got argument to start at " . $from_date->format("Y-m-d"));
				//check is monday
				$what_weekday = $from_date->format('w');
				error_log("That is day $what_weekday");
				//$what_weekday--;//0-based

				//$from_date->sub(new DateInterval("P$what_weekday"."D"));//make monday

				$to_date = clone $from_date;
				$to_date->add(new DateInterval("P7D"));

				$data["from_date"] = $from_date;
					
			}
			break;
		case "month":

			//todo: ta bort d-1 dagar från idag = $from_date
			if($day_in_month>0){
				$span_string = "P".$day_in_month."D";
				$from_date = $today->sub(new DateInterval($span_string));//obs. s.o.
			}
			else{
				$from_date = $today;
			}
			//$to_date = //todo: lägg på en månad, dra av en dag
			$to_date = $from_date;
			$to_date->add("P1M");
			$to_date->sub("P1D");
			break;
		}//TODO: fetch only jobs within time-scope

		$this->load->library('calendar_', array('printer' => $printer));
		$this->calendar_->logHello();

		$data['jobUrl'] = site_url('jobs/edit/');
		if($level==3){//staff
			error_log("Ska hämta jobb, för en pers");
			$data['jobs'] = $jobs = $this->job_model->get_staffmembers_jobs_dates($user_id, $from_date->format("Y-m-d"), $to_date->format("Y-m-d"));
			$data['jobUrl'] = site_url('jobs/view/');

			$data['repetitions'] = $repetitions = $this->job_model->get_staffmembers_repetitions_dates($user_id, $from_date->format("Y-m-d"), $to_date->format("Y-m-d"));
			//error_log("Got repetitions: " . print_r($repetitions, true));
		}
		else if($level == 2){
			error_log("Ska hämta jobb för företaget");
			$data['jobs'] = $jobs = $this->job_model->get_jobs_for_comp_dates($this->staff_model->get_agents_company_id(), $from_date->format("Y-m-d"), $to_date->format("Y-m-d"));

			error_log("Found " . count($jobs) . " jobs");
			/*test*/
			$newEvents = array();
			foreach($jobs as $jo){
			    error_log("Job found: " . print_r($jo, true) . "----------------------------------");
				$this->calendarevent->setStart(new DateTime($jo["datum_start"]))
					->setEnd(new DateTime($jo["datum_slut"]))
					->setDescription($jo["beskrivning"])
					->setOriginId($jo["id"]);

				$newEvents[] = $this->calendarevent;
				error_log("json: " . print_r($this->calendarevent->jsonSerialize(), true));
			}
			if(count($jobs)>0){
				$this->calendar_->setEvents($newEvents);
			}
			else{
				error_log("No jobs found within dates");
			}


			$data["repetitions"] = $repetitions = $this->job_model->get_repetitions_for_comp_dates(
				$this->staff_model->get_agents_company_id(),
				$from_date->format("Y-m-d"), $to_date->format("Y-m-d")
			);
			/*repetition should now include arbetstillfalle_id, datum_start, datum_slut */
			/*the rest info should be derived from origin/arbetstillfalle_id*/

			error_log("Level is 2 & got repetitions : ");
			if(is_array($repetitions)){
				error_log("repetitions: ls" . count($repetitions), 3, '/var/log/php_errors.log');
			}
			else{
				error_log("none");
			}
		}
		else if($level<3 && $level>0){//admin
			error_log("Ska hämta jobb, för alla");
			$data['jobs'] = $jobs = $this->job_model->get_jobs_view_2_dates($from_date->format("Y-m-d"), $to_date->format("Y-m-d"));//view_2 hämtar jobb och antal anknyten personal
		}

		//error_log("Har hämtat jobb: " . print_r($jobs, true));
		//personal för alla jobb
		$staff = array();

		foreach($jobs as $job):

		$staff_here = $this->job_model->get_staff_clean($job["id"]);

		foreach($staff_here as $person){
			array_push($staff, $person);
		}
		endforeach;

		//error_log("Staff: " . print_r($staff, true));

		$staff = array_unique($staff,SORT_REGULAR);//tar bort dubletter
		$staff = array_values($staff);//gör om keys från grunden 0,1,2

		$number_replace = array();//för att dölja id_nummer på personal


		/*lägg till tillfälligt säkert nummer för personal*/
		for($i=0; $i<sizeof($staff); $i++){
			$number_replace[$staff[$i]["id"]] = $i;
			$staff[$i]["nummer"] = $i;//lägger in nytt nummer
		}

		/*ta fram en lista med jobb och personal, med ett säkert nummer istället för id*/
		$link_jobs_staff = $this->job_model->get_staff_link_table();

		for($i=0; $i<sizeof($link_jobs_staff); $i++){
			if(isset($number_replace[$link_jobs_staff[$i]["id_person"]])){
				/*om number_replace har en key mer linkens person-id*/
				$link_jobs_staff[$i]["nummer_person"]=$number_replace[$link_jobs_staff[$i]["id_person"]];
			}
		}


		$data["link_jobs_staff"] = $link_jobs_staff;
		$data["all_staff"] = $staff;
		//$data["today"] = new DateTime();
		/*
		$calendar_types = array();
		$calendar_types[] = "day_view";
		$calendar_types[] = "week_view";
		$calendar_types[] = "month_view";
		*/
		/* first version of calendar ... */
		$data["calendar_type"] = $calendar_type;
		$data["date_span"] = "Alla jobb";//todo: fyll utifrån calendar_type m.m.

		switch($calendar_type){
			case "day":
			$view_ = "calendar_day";
			break;
			case "week":
			$view_ = "calendar_week";
			break;
			case "month":
			$view_ = "calendar_month";
			break;
			default:
			$view_ = "calendar";
		}

		/*2:nd version of calendar - class! */

		$data["calendar"] = $this->calendar_;


		header_view_footer('job/'.$view_, $data);
	}

	public function link_list(){
		$level = $this->user_model->get_level();
		$comp = $level>1 ? $this->staff_model->get_agents_company_id() : FALSE;//function uses false

		$data["link_table"] = $this->job_model->get_staff_link_table($comp);
		$data["title"] = "Länktabell personal - jobb";
		$data["level"] = $level;

		header_view_footer('job/link_table', $data);

	}

	public function link_list_nullcheck(){
		$data["link_table"] = $this->job_model->get_staff_link_table_nullcheck();
		$data["title"] = "Länktabell personal - jobb med tomt för ogiltiga poster";

		header_view_footer('job/link_table', $data);
	}

	public function create()
	{
		$this->load->model('workplace_model');

		$this->load->helper('form');
		$this->load->library('form_validation');

		$data['title'] = 'Spara info om arbetstillfälle / jobb';

		$level = $this->user_model->get_level();
		if($level == 1){
			$data['workplaces'] = $this->workplace_model->get_workplaces();
		}
		else if($level == 2){
			$data['workplaces'] = $this->workplace_model->get_workplaces_for_company($this->staff_model->get_agents_company_id());
			error_log(print_r($data['workplaces'], true));
		}

		//för script
		$data['head_script'] = "var myCalendar; function doOnLoad() { myCalendar = new dhtmlXCalendarObject(['cal_1','cal_2']);	}";
		$data['head_ext_script'] = "<script src=\"".base_url()."js/dhtmlxcalendar.js\"></script>";
		$data['head_ext_script'] .= "<script src=\"".base_url()."js/Script.js\"></script>";
		$data['head_ext_css'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/roboto.css\"/><link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/dhtmlxcalendar.css\" /><link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";

		$this->form_validation->set_rules('workplace', 'Arbetsplats-id', 'trim|required');
		$this->form_validation->set_rules('start', 'Startdatum och tid', 'trim|required');
		$this->form_validation->set_rules('end', 'Slutdatum och tid', 'trim|required');
		$this->form_validation->set_rules('description', 'Beskrivning', 'trim');


		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('job/create', $data);
		}
		else
		{
			$last_id = $this->job_model->set_job();

			if($last_id){
				$b64 = base64_encode($last_id);
				$edit_job_url = anchor("jobs/edit/$b64", 'anknyta personal');
				$data["message"] = "Nu kan du $edit_job_url";
				header_view_footer('job/success', $data);

			}
			else{
				header_view_footer('pages/error');

			}
		}
	}

	public function edit($id, $obfuscated = FALSE){
		$this->load->model('workplace_model');
		$this->load->model('customer_model');
		$this->load->model('schema_model');
		$this->load->helper('calendar_helper');
		$this->load->library('calendarevent');//for new kind of calendar-class
		$id = base64_decode($id);


		if(!is_numeric($id)){
			$data['heading'] = 'Det fattas uppgifter';
			$data['message'] = 'Inget kan därför göras.';
			header_view_footer('errors/html/error_general', $data);
			return;
		}

		if($obfuscated == "yes"){
			$id = $id -4;
			$id = $id/3;
		}
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Redigera arbetstillfälles uppgifter';
		/*
		 * data[job] får... id, arb_pl_id, arb_plats, datum_start, datum_slut, beskrivning, schema_id
		 */
		$data['job'] = $job = $this->job_model->get_jobs_view_1($id);
		$data['related_workplaces'] = $this->customer_model->get_related_workplaces($id);
		//error_log("Get schema from job: " . print_r($job, true));
		if(isset($job["schema_id"])){
			$schema = $this->job_model->get_related_schema($job["schema_id"]);
		}
		else{
			$schema = null;
		}
		$data['related_schema'] = $schema;
		$data['schema_phrase'] = $this->schema_model->parse_schema_code($schema['schema_kod'], true);
		$data['staff'] = $this->job_model->get_staff($id);
		//error_log(print_r($data['staff'], true));
		$data['workplaces'] = $this->workplace_model->get_workplaces();
		$data['head_ext_css'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/roboto.css\"/><link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/dhtmlxcalendar.css\" />";
		$data["head_ext_css"] .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";
		$data["head_ext_css"] .=  '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>';

		//för script
		$data['head_script'] = "var myCalendar; function doOnLoad() { myCalendar = new dhtmlXCalendarObject(['cal_1','cal_2']);	}";
		$data['head_ext_script'] = "<script src=\"".base_url()."js/dhtmlxcalendar.js\"></script>";
		$data['head_ext_script'] .= '<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==" crossorigin=""></script>';

		if($schema && $this->schema_model->parse_schema_code($schema["schema_kod"])){
			$jobStart = new DateTime($job["datum_start"]);//klarlägg månad
			$limit1 = $jobStart->format("Y-m") . "-01";
			$nextMonth = clone $jobStart;
			$nextMonth->modify("+61 day");
			$limit2 = $nextMonth->format("Y-m-d");
			$data['repetitions'] =
			$repetitions =
				$this->schema_model->generate_events_from_jobschema(
					$job["datum_start"],
					$schema["schema_kod"],
					$limit1,
					$limit2,
					61);
		}

		header_view_footer('job/edit', $data);
	}

	public function update(){
		error_log("update (job)");
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Uppdatera ett arbetstillfälles uppgifter';
		//test
		$form_data = $this->input->post();
		//print_r($form_data);
		$data['job'] = $this->job_model->get_jobs_view_1($form_data["id"]);


		$this->form_validation->set_rules('id', 'Id', 'trim|required');
		$this->form_validation->set_rules('workplace', 'Namn på arbetsplats', 'trim|required');
		$this->form_validation->set_rules('start', 'Start-datum och tid', 'trim|required');
		$this->form_validation->set_rules('end', 'Slut-datum och tid', 'trim|required');
		$this->form_validation->set_rules('description', 'Beskrivning', 'trim');

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('job/edit', $data);
		}
		else
		{
			error_log("job validation passed");
			$result = $this->job_model->update_job();
			$data["title"] = "Uppdatering av ett arbetstillfälle";
			if($result){
				header_view_footer('job/success', $data);
			}
			else{
				header_view_footer('pages/error');
			}

		}
	}

	public function delete($id){
		error_log("Jobs delete");
		if(empty($_SESSION["user_name"])){
			redirect('/');

		}

		if($this->user_model->get_level() > 2){ //om ej admin
			redirect('/');
		}
		$id = base64_decode($id);
		$data['title'] = 'Delete a report record';

		if(is_numeric($id)){
			if($this->job_model->delete_job($id)){
				header_view_footer('job/success');
			}
			else{
				echo "Gick inte";
				$data["message"] = "Gick inte att ta bort jobb";
				header_view_footer("pages/error");
			}
		}
	}

	//ta bort rader från personal_arbetstillfalle där arbetstillfälle ej har något motsvarande i tabell arbets_tillfalle
	public function clear_empty_links_from_links_table($view){
		//TODO: supply a "link_table" in data
		$affected_rows = $this->job_model->delete_empty_links_from_links_table();
		$data["title"] = "Länktabell personal - jobb";
		$data["link_table"] = $this->job_model->get_staff_link_table();//test
		header_view_footer('job/'.$view, $data);//link_table

	}

	public function connect_any_staff($id_job){
		$id_job = base64_decode($id_job);
		error_log("connect_any_staff $id_job");
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Lägg till en jobb-personal förbindelse';

		$level = $this->user_model->get_level();

		if(is_numeric($id_job)){
			$data['job'] = $this->job_model->get_jobs_view_1($id_job);
			//print_r($data);
			$data['header'] = 'Lägg till personal till arbetstillfället på ' . $data["job"]["arb_plats"];

			if($level == 1){
				$data['unrelated_staff'] = $this->job_model->get_unrelated_staff($id_job);/*TODO: korrigera*/
			}
			else if($level == 2){
				$data['unrelated_staff'] = $this->job_model->get_unrelated_staff_company($id_job, $this->staff_model->get_agents_company_id());
			}


			header_view_footer('job/edit_staff', $data);//,$data ?
		}
	}

	public function add_staff(){
		error_log("add_staff");
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Koppla personal till arbetstillfälle';
		$data['header'] = "Koppla personal till arbetstillfälle";
		//$data['workplace'] = $this->workplace_model->get_workplaces($form_data["id"]);
		$this->form_validation->set_rules('job', 'Jobb id', 'trim|required');
		$this->form_validation->set_rules('person', 'Personal id', 'trim|required');

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('job/edit', $data);
		}
		else
		{//TODO: kontroll av att person hör till aktuellt företag
			error_log("Validering klar");
			$person = $this->input->post('person');//id
			$jobb = $this->input->post('job');//id
			error_log("Fick pers $person och jobb $jobb");

			$person_ = $this->staff_model->get_staff_by_id($person);
			$jobb_ = $this->job_model->get_jobs_view_1($jobb);
			$email = $person_["email"];
			$company = $this->company_model->get_company_by_id($this->staff_model->get_staffmembers_company_id($person));//info om systemföretaget
			$comp_name = $company["name"];

			$arbetsplats = $jobb_["arb_plats"];
			$datum_start = $jobb_["datum_start"];
			$datum_slut = $jobb_["datum_slut"];
			$beskrivning = $jobb_["beskrivning"];

			$rubrik = "Du har tilldelats ett jobb från $comp_name";
			$meddelande = "Administratör på $comp_name har tilldelat dig ett jobb.\n\nArbetsplats-namn: $arbetsplats\n\nDatum och tid: $datum_start - $datum_slut\n\nBeskrivning: $beskrivning\n\nSe hemsidan för mer info:\n" . base_url("index.php/") . "\n";
				
			error_log("Ska anrop job_model->add_staff()");
			$res = $this->job_model->add_staff();
			if($res==1){
				//$this->staff_model->send_mail($email_,$rubrik,$meddelande);//TODO: mail via ZOHO och eller intern mail
				$b64 = base64_encode($jobb);
				$edit_again_link = anchor("jobs/edit/$b64", "åter till jobbet ($beskrivning)");
				$data["message"] = "Vill du $edit_again_link ?";

				header_view_footer('job/success', $data);
			}
			else{
				$data["message"] = "Kunde inte anknyta person.";
				if($res == 1062){
					$data["message"] .= " Personen är redan anknyten.";
				}

				header_view_footer('pages/error', $data);
			}
		}
	}

	public function delete_staff_connection($id_job, $id_staff, $view){

		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		$job = base64_decode($id_job);
		$person = base64_decode($id_staff);
		$this->user_model->write_log("I delete_staff_connection med $id_job - $job, $id_staff - $person, och $view");
		if($view === "s"){
			$view = 'staff/success';
		}
		else if($view === "j"){
			$view = 'job/success';
		}
		else{
			$view = '/';
		}
		$data['title'] = 'Ta bort en koppling arbetstillfälle - personal';

		if(is_numeric($job) && is_numeric($person)){
			$this->user_model->write_log("Båda id-värdena är numeriska");
			if($this->job_model->delete_connection_record($job, $person)){
				$edit_again_link = anchor("jobs/edit/$id_job", "redigera jobbet mer");
				$data["message"] = "Kopplade bort en person, du kan $edit_again_link";
				header_view_footer($view, $data);
			}
			else{
				echo "Gick inte";
			}
		}

		else{
			echo "Saknas rätt input";
		}
	}

	public function add_schema($id_job){
		//add a schema or reuse (not reveiled)
		$this->load->helper('form');
		$this->load->library('form_validation');
		$job = base64_decode($id_job);
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/schema.css\" /><link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";

		$data['title'] = 'Skapa ett schema för jobbet...';
		if(is_numeric($job)){
			//control id of job vs user/company
			$level = $this->user_model->get_level();
			if($level<1 || $level>2){
				exit("No access here");
				return;
			}
			else if($level==2){
				$comp_id = $this->staff_model->get_agents_company_id();
				$access = $this->job_model->is_job_for_company($comp_id, $job);
				if(!$access){
					exit("No access here");
					return;
				}
			}
			else if($level==1){
			}

			$data["job"] = $jobb_ = $this->job_model->get_jobs_view_1($job);
			$data['job_description'] = $jobb_["beskrivning"];
			$data['job_id'] = $jobb_["id"];
			$data["start_date"] = $jobb_["datum_start"];
			header_view_footer('job/add_schema', $data);
		}
		else{
			header_view_footer('pages/error');
		}
	}

	public function remove_schema($id_job){
		error_log("remove_schema($id_job)");
		$job = base64_decode($id_job);

		$data['title'] = 'Ta bort schema';

		if(is_numeric($job)){
			$level = $this->user_model->get_level();
			if($level<1 || $level>2){
				exit("No access here");
				return;
			}

			$success = $this->job_model->unset_schema($job);

			if($success){
				$b64 = $id_job;
				$edit_again_link = anchor("jobs/edit/$b64", "åter till jobbet");
				$data["message"] = "Vill du $edit_again_link?";
				header_view_footer('job/success', $data);
				return;
			}
		}
		
		header_view_footer('pages/error');
	}


}
?>
