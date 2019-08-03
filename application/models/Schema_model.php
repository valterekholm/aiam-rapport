<?php
class Schema_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function create(){

	}

	//Hämtar jobb, utan anknyten data
	public function get_schemas(){
		error_log("get_schemas");

		$query = $this->db->get('jobb_schema');
		return $query->row_array();
	}
	/*
	public function get_schemas_for_company($company_id){
		$sql = "SELECT DISTINCT js.* FROM jobb_schema js
			JOIN arbets_tillfalle at
			ON (arbetstillfalle_id = at.id)
			LEFT JOIN kunder_arbetsplatser ka
			ON (ka.id_arbetsplats = at.arbetsplats_id)
			LEFT JOIN kund
			ON (kund.id = ka.id_kund)
			LEFT JOIN companies_kunder ck
			ON (ck.id_kund = kund.id) WHERE id_company = $company_id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}*/

	public function get_schemas_for_company($company_id){
		$this->load->model("job_model");

		$jobs_for_comp = $this->job_model->get_job_schema_ids_for_company($company_id);

		if(count($jobs_for_comp)==0){
			return false;
		}
		$ids = array();
		//error_log(print_r($jobs_for_comp, true));//array [ array[]]
		foreach($jobs_for_comp as $job){
			$ids[] = $job["schema_id"];
			error_log("sh id ".$job["schema_id"]);
		}

		$sql = "SELECT * FROM jobb_schema WHERE id IN (" . implode(",",$ids) . ")";

		$query = $this->db->query($sql);
		//error_log($sql);
		return $query->result_array();
	}


/*
	public function get_schemas_for_staffmember($staff_id){
		error_log("get_schemas_for_staffmember($staff_id)");
		$sql = "SELECT * FROM jobb_schema js
			JOIN arbets_tillfalle at
			ON (arbetstillfalle_id = at.id)
			JOIN personal_arbetstillfalle
			ON (id_arbetstillfalle = at.id)
			WHERE id_person = $staff_id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}*/
/*
	public function get_schemas_for_job($job_id){
		error_log("get_schemas_for_job($job_id)");
		$sql = "SELECT * FROM jobb_schema " .
			"WHERE arbetstillfalle_id = $job_id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}*/

	public function get_schemas_for_dropdown(){
		$level = $this->user_model->get_level();

		$query = $this->db->query('SELECT id, schema_kod FROM jobb_schema');
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[$row->id] = $row->name;
			}
			return $result;
		}
		else return null;
	}


	public function get_schema_by_id($id=FALSE){
		if($id === FALSE){
			return;
		}

		$query = $this->db->get_where('jobb_schema', array("id" => $id));
		return $query->row_array();
	}
	public function get_schema_by_code($code=FALSE){
		error_log("get_schema_by_code($code)");
		if($code === FALSE){
			return;
		}

		$query = $this->db->get_where('jobb_schema', array("schema_kod" => "$code"));
		if($query){
			return $query->row_array();
		}
		return false;
	}


	public function set_schema(){
		$this->load->helper('url');
		$data = array(
			"schema_kod" => $this->input->post("schemacode")
		);
		$result = $this->db->insert('jobb_schema', $data);
		$last_id = $this->db->insert_id();
		if($result){
			return $last_id;
		}
		else{
			return false;
		}
	}
/*
	public function job_has_schema($job_id){
		$sql = "SELECT count(*) antal_scheman FROM jobb_schema WHERE arbetstillfalle_id = $job_id";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		return $row["antal_scheman"]>0;
	}*/

	public function update_schema(){
		$this->load->helper('url');
		$id = $this->input->post('id');
		$data = array(
			'schema_kod' => $this->input->post('schemacode')
		);

		error_log("Ska uppdatera med data " . print_r($data, true));
		$this->db->where('id', $id);
		$result = $this->db->update('jobb_schema',$data);
		//error_log($this->db->last_query());
		return $result;

	}

	/*job: a fetched db result object, for a job
	 * from date, to get repetitions from - target, string
	 * to date, to when to get repetitions - target, string
	 * */
	public function get_events_from_job_dateframe($job, $from, $to){
		error_log("get_events_from_job_dateframe, for job " . $job["id"] . ", from/to $from/$to ");
		if(empty($job["schema_id"])){
			error_log("Job has empty schema");
			return false;
		}

		$jobStart = new DateTime($job["datum_start"]);
		$jobEnd = new DateTime($job["datum_slut"]);
		$frameEnd = new DateTime($to);

		$jobDuration = $jobStart->diff($jobEnd);

		$jobAccrossDates = $jobStart->format("Y-m-d") != $jobEnd->format("Y-m-d");



		if($frameEnd < $jobStart){
			/*If the job starts after the requested timeframe*/
			return null;
		}


		$schema = $this->get_schema_by_id($job["schema_id"]);

		$events = array();

		$evnts = $this->generate_events_from_jobschema($job["datum_start"], $schema["schema_kod"], $from, $to);
		error_log("Got nr of events: " . count($evnts));

		$from_time = $job["datum_start"];
		$to_time = $job["datum_slut"];

		$from_time = explode(" ", $from_time, 2)[1];
		$to_time = explode(" ", $to_time, 2)[1];/*get the clock part*/



		foreach($evnts as $e){
			//spara arbetstillfälle, datum_start, datum_slut (datetime)
			$dst = "$e $from_time";	
			if($jobAccrossDates){
				$end_date = new DateTime("$e $to_time");
				$end_date->modify($jobDuration->format('%R%a day'));
				$dsl = $end_date->format("Y-m-d H:i");
			}
			else{
				$dsl = "$e $to_time";
			}

			$edata = array("arbetstillfalle_id" => $job["id"], "datum_start" => $dst, "datum_slut" => $dsl);
			$events[] = $edata;/*TODO: make class CalendarEvent*/
		}

		//error_log("Genererade events: " . print_r($events, true));

		return $events;
	}

	/*this function creates a schema with supplied code if no similar is found in db, in any case returns id of schema*/
	public function get_id_schema_saved_or_existing($code){
		$found = $this->get_schema_by_code($code);

		if($found){
			error_log("The code $code found");
			return $found["id"];
		}

		error_log("The code $code NOT found");

		$data = array(
			"schema_kod" => "$code"
		);
		$result = $this->db->insert('jobb_schema', $data);
		$last_id = $this->db->insert_id();
		error_log("Insert last id: $last_id");
		return $last_id;
	}

	/*
	 * start_date - string
	 * schema_code - string
	 * limit_start - string
	 * limit_end - string
	 */
	public function generate_events_from_jobschema($start_date, $schema_code, $limit_start, $limit_end, $repetitions=1000){
		$this->load->helper("screen_out_helper");

		error_log("generate_events_from_job_schema($start_date, $schema_code, $limit_start, $limit_end, $repetitions)");


		$startDate = new DateTime($start_date);
		$limit1 = new DateTime($limit_start);
		$limit2 = new DateTime($limit_end);
		if(!$limit1 || !$limit2 || !$start_date){
			return false;
		}

		$EXCLUDE_ORIGIN_DATE = true; //true, so it's not double on that day




		/*TODO: make sure the date-window limit_start to limit_end is reached/covered by repetitions*/
		//$job = 
		$schema_base = $this->parse_schema_code($schema_code);
		$di = $schema_base["di"];
		$wi = $schema_base["wi"];
		$mi = $schema_base["mi"];

		error_log("Schema_base: " . print_r($schema_base, true));


		if(isset($schema_base["weekDays"])){

			$weekDays = $schema_base["weekDays"];
		}
		else{
			$weekDays = null;
		}

		$noDaySent = $di == 0 && null == $weekDays;
		$noWeekSent = $wi == 0;

		error_log("noDaySent: " . text_boolean_of_expression($noDaySent));

		if($di<0 || $wi<0 || $mi<0){
			exit;
		}

		/*if di>0 then weekDays are forgotten
		 * so day-interval has higher precedence*/
		if($di>0 && !empty($weekDays)){
			unset($weekDays);
		}

		/*else if($di == 0 && $wi == 0 && $mi > 0){
			error_log("only month i; $mi");
	}*/

		/*if using weekDays or if di is 0*/
		else if($di==0){
			$di = 1;
		}

		/*handle empty (0) wi/mi*/
		if($wi==0){
			$wi=1;
		}
		if($mi==0){
			$mi=1;
		}

		$has7 = false;
		$has0 = false;
		$faultyDay = false;
		if(!empty($weekDays)){
			foreach($weekDays as $d){
				$faultyDay = $d<0 || $d>7;
				if($d==7){ $has7 = true; }
				if($d==0){ $has0 = true; }
			}
		}
		if($faultyDay){
			exit("faulty day");
		}
		$remove7 = $has7 && $has0;



		/*replace 7 with 0, to follow php syntax of weekdays 0-6*/
		if($has7){
			reset($weekDays);//?
			$k= array_search(7, $weekDays);
			if($remove7){
				unset($weekDays[$k]);
			}
			else{
				$weekDays[$k] = 0;
			}
		}

		/*di is different from wi and mi, because di must always be used (dayWalker)*/


		/*echo "<h3>Settings:</h3>";
		echo "day interval: $di<br>";
		echo "week interval: $wi<br>";
		echo "month interval: $mi<br>";*/




		$limit1 = new DateTime($limit_start);
		$limit2 = new DateTime($limit_end);

		$dayRunner = clone $startDate;
		$daysSerie = array();
		$result = array();

		/*filling daysSerie based upon di, not weekDays*/
		if(empty($weekDays)){
			for($i=0; ;$i++){
				/*Days to come according to di*/
				/*if within date frame*/
				if($dayRunner >= $limit1 && $dayRunner < $limit2
				&& $dayRunner->format("Y-m-d") != $startDate->format("Y-m-d")){
					$daysSerie[] = $dayRunner->format("Y-m-d");
				}
				else if($dayRunner >= $limit2){
					break;
				}

				$dayRunner->modify("+$di day");
			}
		}

		/*filling daysSerie based on weekDays*/
		else{
			$count = 0;
			do{
				if(in_array($dayRunner->format("w"), $weekDays)){
					if($dayRunner >= $limit1 && $dayRunner < $limit2
					&& $dayRunner->format("Y-m-d") != $startDate->format("Y-m-d")){
						$daysSerie[] = $dayRunner->format("Y-m-d");
					}
					$count++;
				}
				$dayRunner->modify("+1 day");
			}while(/*$count < $repetitions*/ $dayRunner < $limit2);
		}


		$lastFromDays = new DateTime(end($daysSerie));


		/*Weeks interval setup*/
		if($wi>0){

			/*make it last day in week so can compare it with weekrunner*/
			$lastFromDaysInWeek = clone $lastFromDays;
			while($lastFromDaysInWeek->format("w") != 0){
				$lastFromDaysInWeek->modify("+1 day");
			}

			/*weeks serie should not go beyond days serie*/

			$weeksRunner = clone $startDate;
			$weeksSerie = array();

			/*limit is the last day from daysSerie*/
			while($weeksRunner <= $lastFromDaysInWeek){
			/*Weeks to come according to wi*/
				if($weeksRunner >= $limit1 && $weeksRunner < $limit2){
					$weeksSerie[] = $weeksRunner->format("o W");
				}
				$weeksRunner->modify("+$wi week");
			}

		}
		/*end weeks interval setup*/

		/*Month interval setup*/
		if($mi>0){
			error_log("Go for monthRunner");

			$lastFromDaysInMonth = clone $lastFromDays;

			while($lastFromDaysInMonth->format("d") != "01"){
				$lastFromDaysInMonth->modify("+1 day");
			}
			/*go back to last day in month*/
			$lastFromDaysInMonth->modify("-1 day");


			$monthRunner = clone $startDate;
			$monthsSerie = array();

			while($monthRunner <= $lastFromDaysInMonth){
				/*Month to come according to mi*/
				if($monthRunner >= $limit1 && $monthRunner < $limit2){
					$monthsSerie[] = $monthRunner->format("Y m");
					error_log("Added " . $monthRunner->format("Y m"));
				}
				$monthRunner->modify("+$mi month");
			}

		}
		/*End month interval setup*/

		error_log("noDaySent " . text_boolean_of_expression($noDaySent) . ", mi: $mi, wi: $wi");
		/*check and save wich days intersects
		 * if di and wi are set*/

		if($noDaySent && $noWeekSent && $mi>0){
			error_log("test special case");
			/*test special case, only month sent as interval*/
			foreach($monthsSerie as $md){
				$dayNr = $startDate->format("d");/*numeric day of greg. month*/

				$monthNr = explode(" ", $md)[1];

				$yearNr = explode(" ", $md)[0];

				$tempDate = $yearNr . "-" . $monthNr . "-" . $dayNr;

				while(! checkdate ( intval($monthNr) , intval($dayNr) , intval($yearNr) )){
					$dayNr--;
				}//step down day in case its out of month

				$result[] = $yearNr . "-" . $monthNr . "-" . $dayNr;
			}
		}


		else if($di>0 && $wi>0 && $mi<2){/*if $mi would be 1 (each month), it would still not affect*/
			foreach($daysSerie as $date){/*strings*/
				$dTest = new DateTime($date);
				$test = $dTest->format("o W");/*o instead of Y: ISO-8601 week-numbering year. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead. (added in PHP 5.1.0)*/
				if(in_array($test, $weeksSerie)){/*yyyy-W year and week-number*/
					$result[] = $date;
				}
			}
		}
		/*if di, wi and mi are set*/
		else if($di>0 && $wi>0 && $mi>0){
			foreach($daysSerie as $date){
				$dTest = new DateTime($date);
				$testW = $dTest->format("o W");
				$testM = $dTest->format("o m");
				if(in_array($testW, $weeksSerie) && in_array($testM, $monthsSerie)){
					$result[] = $date;
				}
			}
		}
		/*if di and mi are set*/
		else if($di>0 && $wi<2 && $mi>0){
			foreach($daysSerie as $date){
				$dTest = new DateTime($date);
				$testM = $dTest->format("o m");
				if(in_array($testM, $monthsSerie)){
					$result[] = $date;
				}
			}
		}

		return $result;

	}

	//first version
	//now used to define di, wi, mi and weekDays
	//returns array with those
	//or if justPhrase; an explanating text
	public function parse_schema_code($code, $justPhrase = false){
		error_log("parse_schema_code $code");

		$vd = array("söndag","måndag","tisdag","onsdag","torsdag","fredag","lördag","söndag");


		$d=0;//day is 1 as default, meaning every day, if no w(eek) or m(onth) are found
		$w=0;
		$m=0;

		$phrase = "";//if justPhrase, fill, return

		$code = strtolower($code);

		if($code == ""){
			$code == "d";//everyday
			//$phrase = "varje dag";
		}

		$array = str_split($code);

		$levels = array("day","week","month");
		$level =0;//1=day 2=week 3=month

		$gotD = false;
		$gotW = false;
		$gotM = false;
		$gotX = false;

		$count = 0;
		$hasWeekdays=false;//any inital digits representing week days, like 12 mond tuesd
		$weekDays = array();
		$letterCount =0;
		$isCombo = substr($code, -1) == 'x';
		foreach($array as $char){

			if($count==0){
				$hasWeekdays = ctype_digit($char);
			}

			if(ctype_alpha($char)){
				$letterCount++;

				if($char == "d"){
					if(!$gotD && !$gotW && !$gotM){
						$level=1;
					}
					else{
						error_log("Fel: d i fel plats");
						return false;
					}
				}
				else if($char == "w"){
					if(!$gotW && !$gotM){
						$level =2;
					}
					else{
						error_log("Fel: w i fel plats");
						return false;
					}
				}
				else if($char == "m"){
					if(!$gotM){
						$level = 3;
					}
					else{
						error_log("Fel: m i fel plats");
						return false;
					}
				}
				else if($char == "x"){
					/*$gotX = true;/*not impl*/
					error_log("Fel: x ej impl");
					return false;
				}
			}

			else if(ctype_digit($char)){/*siffra*/
				if($level == 0){
					$iv = intval($char);
					if($iv==0){ $iv=7;/*sunday*/ }
					if($iv<1 || $iv>7){
						error_log("Fel: för hög dagsiffra");
						return false;
					}
					else if(in_array($iv, $weekDays)){
						error_log("dublett av dagsiffra");
						return false;
					}
					$weekDays[] =$iv;
				}
				else if($level == 1){
					/*days interv?*/
					$d .= $char;
				}
				else if($level == 2){
					if($w==0){ $w=""; }
					$w .= $char;
				}
				else if($level == 3){
					if($m==0){ $m=""; }
					$m .= $char;
				}

			}

			$count++;
		}
		if($hasWeekdays){
			//$d=1;/*step*///taken care of in function generate_events_from_jobschema()
			$phrase .= "veckodagar "; //. implode(",", $weekDays) . "; ";

			foreach($weekDays as $key => $val){
				$phrase .= $vd[$val] . ",";
			}
			$phrase = rtrim($phrase, ",");
			$phrase .= "; ";
		}
		else if($w==0 && $m==0 && $d==0){
			$d=1;
			$phrase .= "varje dag; ";
		}
		if($w<2){
			$gotW = false;
		}/*this is used for combinations of d and w*/
		else{
			$gotW = true;
		}
		if($d == 1){
			$phrase .= "varje dag; ";
		}
		if($d > 1){
			$phrase .= "dagsinterval $d; ";
		}
		if($m<2){
			$gotM = false;
		}/*this means no interrupt*/
		else{
			$gotM = true;
		}
		error_log("d $d, w $w, m $m");
		if($hasWeekdays){
			error_log("has weekdays");
		}
		if($w==1){
			$phrase .= "varje vecka; ";
		}
		else if($w>1){
			$phrase .= "veckointervall $w; ";
		}
		if($m==1){
			$phrase .= "varje månad; ";
		}
		else if($m > 1){
			$phrase .= "månadsinterval $m; ";
		}
		$result = array("di"=>$d, "wi"=>$w, "mi"=>$m);
		if($hasWeekdays){
			$result["weekDays"] = $weekDays;
		}

		if($justPhrase){
			return $phrase;
		}

		return $result;

	}
}
?>
