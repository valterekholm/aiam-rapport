<?php
class Reports_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->model('user_model');
		date_default_timezone_set('Europe/Stockholm');//för check in...
	}

	public function create(){

	}

	public function get_reports($id=FALSE){
		//echo "get_staff med " . $id . "<br>";
		if($id === FALSE){
			$query = $this->db->get('arbets_rapport');
			return $query->result_array();
		}

		$query = $this->db->get_where('arbets_rapport', array('id' => $id));
		return $query->row_array();
	}

	//för edit-sida
	//obs funkar ej om inte arbets_tillfälle är specificerat - ger tomt res.
	public function get_reports_view_2($id=FALSE){
		$this->user_model->write_log("Reports get_reports_view_2");
		if($id === FALSE){
			//select arbets_rapport.id, arbets_tillfalle.datum_start jobb_start, fornamn, arbetsplats.namn arbetsplats, check_in_time, lati_in, longi_in, check_out_time, lati_out, lati_out from arbets_tillfalle join (arbets_rapport left join personal on (personal_id = personal.id)) on (arbets_tillfalle.id = arbets_rapport.arbetstillfalle_id) join arbetsplats on (arbets_tillfalle.arbetsplats_id=arbetsplats.id);
			$query = $this->db->query("select arbets_rapport.id, arbetstillfalle_id jobb_id, personal_id,
				arbets_tillfalle.datum_start jobb_start, fornamn, arbetsplats.namn arbetsplats,
				check_in_time, lati_in, longi_in,
				check_out_time, lati_out, longi_out,
				benamning, rast_minuter from arbets_tillfalle
				join (arbets_rapport left join personal on (personal_id = personal.id)) on (arbets_tillfalle.id = arbets_rapport.arbetstillfalle_id)
				join arbetsplats on (arbets_tillfalle.arbetsplats_id=arbetsplats.id)");
			return $query->result_array();
		}

		$query = $this->db->query("select arbets_rapport.id, arbetstillfalle_id jobb_id, personal_id,
			arbets_tillfalle.datum_start jobb_start, fornamn, arbetsplats.namn arbetsplats,
			check_in_time, lati_in, longi_in,
			check_out_time, lati_out, longi_out,
			benamning, rast_minuter
			from arbets_tillfalle
			join (arbets_rapport left join personal on (personal_id = personal.id)) on (arbets_tillfalle.id = arbets_rapport.arbetstillfalle_id)
			join arbetsplats on (arbets_tillfalle.arbetsplats_id=arbetsplats.id)
			where arbets_rapport.id = '$id'");
		return $query->row_array();
	}
	//Hämtar rapporter med tillhörande persons namn, antingen samtliga rapporter eller viss persons rapporter
	public function get_reports_view_3($id=FALSE){
		error_log("get_reports_view_3 med id: $id");
		if($id === FALSE || is_null($id) || $id == "NULL"){
			$sql = "select arbets_rapport.id, personal_id, fornamn, 
				efternamn, check_in_time, lati_in,
				longi_in, check_out_time, lati_out,
				longi_out, ceil(TIMESTAMPDIFF(second,check_in_time,check_out_time)/60) as minuter,
				rast_minuter as 'varav rast',
				benamning
				from arbets_rapport
				left join personal
				on (personal_id = personal.id)";
			//echo "<p>inget id... $sql</p>";
			$query = $this->db->query($sql);
			return $query->result_array();
		}

		//todo: kolla om benamning behövs
		$sql = "select arbets_rapport.id, personal_id, fornamn, 
			efternamn, check_in_time, lati_in,
			longi_in, check_out_time, lati_out,
			longi_out,
			ceil(TIMESTAMPDIFF(second,check_in_time,check_out_time)/60) as minuter,
			rast_minuter as 'varav rast',
			benamning
			from arbets_rapport
			left join personal
			on (personal_id = personal.id)
			where personal_id = '$id'";
		//echo "<p>id $id... $sql</p>";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	//$id - id på person
	//$from - datum fr.o.m.
	//$to - datum t.o.m.
	public function get_reports_view_3_date($id=FALSE,$from=FALSE,$to=FALSE){
		//nytt att id kan skickas som 0 vilket motsvarar tomt 25/10 -17
		if($id==0){
			$id=FALSE;//todo: om fungerar, implementera på alla get_reports med $id=FALSE
		}
		$this->user_model->write_log("I get_reports_view_3_date med $id, $from och $to");
		$and_sql = "";
		$and2_sql = "";
		if($from!==FALSE && $to!==FALSE){
			$and_sql = " and ";
		}
		if($id===FALSE){// || is_null($id)
			$where_sql = ($from!==FALSE || $to!==FALSE)?"where":"";
			$from_sql = ($from===FALSE)?"":" date(check_in_time) >= '$from' ";
			$to_sql = ($to===FALSE)?"":" date(check_in_time) <= '$to' ";
			$sql = "select arbets_rapport.id, personal_id, fornamn,
				efternamn, check_in_time, lati_in,
				longi_in, check_out_time, lati_out,
				longi_out, ceil(TIMESTAMPDIFF(second,check_in_time,check_out_time)/60) as minuter,
				rast_minuter as 'varav rast', benamning  
				from arbets_rapport
				left join personal
				on (personal_id = personal.id) $where_sql $from_sql $and_sql $to_sql";
			//echo "get_reports_view_3_date utan id,<br>" . $sql . "<br>";
			$query = $this->db->query($sql);
			//echo "nu_rows: " . $query->num_rows();
			return $query->result_array();
		}
		if($from !== FALSE || $to !== FALSE){
			$and2_sql = " and ";
		}
		$from_sql = ($from===FALSE)?"":" date(check_in_time) >= '$from'";
		$to_sql = ($to===FALSE)?"":" date(check_in_time) <= '$to'";
		$sql = "select arbets_rapport.id, personal_id, fornamn,
			efternamn, check_in_time, lati_in,
			longi_in, check_out_time, lati_out,
			longi_out, ceil(TIMESTAMPDIFF(second,check_in_time,check_out_time)/60) as minuter,
			benamning, rast_minuter as 'varav rast' from arbets_rapport
			left join personal
			on (personal_id = personal.id)
			where $from_sql $and_sql $to_sql $and2_sql personal_id = '$id'";
		//echo "make excel med id ($id): " . $sql;
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_reports_for_company($company_id){
		$level = $this->user_model->get_level();
		if($level>2){
			exit();
		}
		$ids = $this->staff_model->get_companys_staff_ids($company_id);

		error_log("ids of staff in c: " . print_r($ids, true));

		$id_s = implode(",", $ids);


		error_log("get_reports_for_company($company_id)");
		$sql = "select arbets_rapport.id, personal_id, fornamn,
			efternamn, check_in_time, lati_in,
			longi_in, check_out_time, lati_out,
			longi_out, ceil(TIMESTAMPDIFF(second,check_in_time,check_out_time)/60) as minuter,
			rast_minuter as 'varav rast',
			benamning
			from arbets_rapport
			left join personal
			on (personal_id = personal.id) WHERE personal_id IN ($id_s)";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	//Hämtar specifik rapport
	public function get_reports_by_id_view_3($id=FALSE){
		$this->user_model->write_log("Reports_model get_reports_by_id_view_3 med id $id");
		if($id === FALSE){
			return FALSE;//$query->result_array();
		}

		$sql = "select arbets_rapport.id, personal_id, fornamn,
			efternamn, check_in_time ch_i, lati_in,
			longi_in, check_out_time ch_o, lati_out,
			longi_out,
			ceil(TIMESTAMPDIFF(second,check_in_time,check_out_time)/60) as minuter,
			rast_minuter as 'varav_rast',
			benamning
			from arbets_rapport
			left join personal
			on (personal_id = personal.id)
			where arbets_rapport.id = '$id'";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	public function get_users_reports($id=FALSE){
		$this->user_model->write_log("Reports get_users_reports");
		//echo "get_staff med " . $id . "<br>";
		if($id === FALSE){
			return FALSE;
		}

		$query = $this->db->get_where('arbets_rapport', array('personal_id' => $id));
		return $query->result_array();
	}
	public function get_users_last_report($id=FALSE){
		//$this->user_model->write_log("Reports get_users_last_report");
		//echo "get_staff med " . $id . "<br>";
		if($id === FALSE){
			return FALSE;
		}
		$sql = "SELECT * FROM arbets_rapport WHERE personal_id = $id ORDER BY check_out_time DESC LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row();
		if(isset($row)){
			return $row;
		}
		else{
			return false;
		}
	}
	public function set_report()
	{
		$this->user_model->write_log("Reports set_report");
/*
| personal_id        | int(10)      | NO   |     | NULL    |       |
| arbetstillfalle_id | int(10)      | NO   |     | NULL    |       |
| check_in_time      | timestamp    | YES  |     | NULL    |       |
| longi_in           | decimal(9,6) | YES  |     | NULL    |       |
| lati_in            | decimal(9,6) | YES  |     | NULL    |       |
| check_out_time     | timestamp    | YES  |     | NULL    |       |
| longi_out          | decimal(9,6) | YES  |     | NULL    |       |
| lati_out           | decimal(9,6) | YES  |     | NULL    |       |
 */
		$this->load->helper('url');
		$data = array(
			'personal_id' => (($this->input->post('person')!='')?$this->input->post('person'):''),
			'arbetstillfalle_id' => (($this->input->post('job')!='')?$this->input->post('job'):''),
			'check_in_time' => $this->input->post('check_in_time'),
			'longi_in' => $this->input->post('check_in_longi'),
			'lati_in' => $this->input->post('check_in_lati'),
			'check_out_time' => $this->input->post('check_out_time'),
			'longi_out' => $this->input->post('check_out_longi'),
			'lati_out' => $this->input->post('check_out_lati')
		);
		return $this->db->insert('arbets_rapport', $data);
	}

	public function update_report()
	{
		$this->user_model->write_log("Reports update_report");
		$this->load->helper('url');
		$longi_in = $this->input->post('check_in_longi');
		$longi_in = isset($longi_in) ? $longi_in : 'NULL';

		//TODO: sätt NULL om koordinater är tomma

		$data = array(
			'id' => $this->input->post('id'),
			'personal_id' => (($this->input->post('person')!='')?$this->input->post('person'):''),
			'arbetstillfalle_id' => (($this->input->post('job')!='')?$this->input->post('job'):''),
			'check_in_time' => $this->input->post('check_in_time'),
			'longi_in' => $longi_in,
			'lati_in' => $this->input->post('check_in_lati'),
			'check_out_time' => $this->input->post('check_out_time'),
			'longi_out' => $this->input->post('check_out_longi'),
			'lati_out' => $this->input->post('check_out_lati'),
			'rast_minuter' => $this->input->post('rast_m'),
			'benamning' => $this->input->post('benamning')
		);
		error_log("update_report med " . print_r($data, true));

		return $this->db->replace('arbets_rapport',$data);
	}

	public function delete_report($id){
		$this->user_model->write_log("Reports delete_report");
		$this->db->where('id',$id);
		return $this->db->delete('arbets_rapport');
	}

	public function check_in(){
		$this->user_model->write_log("Reports check_in");
		//skapa ny...
		$this->load->helper('url');
		$data = array(
			'personal_id' => base64_decode($this->input->post('id')),
			'check_in_time' => date("Y-m-d H:i:s"),//$this->input->post('check_time'),
			'server_in_time' => 'NOW()',
			'longi_in' => $this->input->post('check_in_lon'),
			'lati_in' => $this->input->post('check_in_lat')
		);
		//print_r($data);
		return $this->db->insert('arbets_rapport', $data);
	}

	public function check_in_no_coords(){
		$this->user_model->write_log("Reports check_in_no_coords");
		$this->load->helper('url');
		$data = array(
			'personal_id' => base64_decode($this->input->post('id')),
			//'arbetstillfalle_id' => (($this->input->post('job')!='')?$this->input->post('job'):''),
			'check_in_time' => date("Y-m-d H:i:s"),//$this->input->post('check_time'),
			'server_in_time' => 'NOW()',
			'benamning' => $this->input->post('check_place')
		);
		return $this->db->insert('arbets_rapport', $data);
	}

	public function check_out(){
		$this->user_model->write_log("Reports check_out");
		//uppdatera
		$this->load->helper('url');
		$data = array(
			//'personal_id' => $this->input->post('id'),
			//'arbetstillfalle_id' => (($this->input->post('job')!='')?$this->input->post('job'):''),
			'check_out_time' => date("Y-m-d H:i:s"),//$this->input->post('check_time'),
			'server_out_time' => 'NOW()',
			'longi_out' => $this->input->post('check_out_lon'),
			'lati_out' => $this->input->post('check_out_lat')
		);
		//print_r($data);
		$this->db->where('id',$this->input->post('report_id'));
		$result = $this->db->update('arbets_rapport', $data);
		if($this->db->affected_rows()>0){
			//request break duration info (minutes)
			$_SESSION["open_report"] = "";
			return $this->input->post('report_id');
		}
		return false;
	}

	public function check_out_no_coords(){
		$this->user_model->write_log("Reports check_out_no_coords");
		//uppdatera
		$this->load->helper('url');
		$data = array(
			//'personal_id' => $this->input->post('id'),
			//'arbetstillfalle_id' => (($this->input->post('job')!='')?$this->input->post('job'):''),
			'check_out_time' => date("Y-m-d H:i:s"),//$this->input->post('check_time'),
			'server_out_time' => 'NOW()',
			'benamning' => $this->input->post('check_place')
		);
		$this->db->where('id',$this->input->post('report_id'));
		$result_ = $this->db->update('arbets_rapport', $data);
		$this->user_model->write_log("Result from check_out_no_coords: $result_, affected rows: " . $this->db->affected_rows());
		if($this->db->affected_rows()>0){
			//request break duration info (minutes)
			$_SESSION["open_report"] = "";
			return $this->input->post('report_id');
		}
		return false;
	}

	public function user_has_unfinished_report(){
		$this->user_model->write_log("Reports user_has_unfinished_report");
		error_log("user_has_unfinished_report");
		/*$this->load->model("taff_model");*/
		error_log("Will try get staff by id " . base64_decode($_SESSION["user_id"]));
		$user = $this->staff_model->get_staff_by_id(base64_decode($_SESSION["user_id"]));
		error_log("Got user " . $user["fornamn"], true);
		$reports = $this->get_users_reports($user["id"]);
		foreach($reports as $report){
			if(empty($report->check_out_time)){
				return TRUE;
			}
		}
		return FALSE;
	}

	public function get_unfinished_report($user_id){
		$this->user_model->write_log("Reports get_unfinished_report");
		$query = $this->db->query("SELECT * FROM arbets_rapport where check_out_time is null and personal_id = '".$user_id."' limit 1");
		return $query->row_array();
	}

	public function check_out_undo(){
		$this->load->helper('url');
		$id = $this->input->post("report_id");//test
		/*
		 * $data = array('name' => $name, 'email' => $email, 'url' => $url);
		 * $where = "author_id = 1 AND status = 'active'";
		 * $str = $this->db->update_string('table_name', $data, $where);*/

		$data = array('check_out_time' => 'NULL', 'longi_out' => 'NULL', 'lati_out' => 'NULL', 'benamning' => 'NULL');
		$where = "id = $report_id";
		$sql = $this->db->update_string('arbets_rapport', $data, $where);
		if($this->db->query($sql)){
			return true;
		}
		else{
			return false;
		}

	}

	public function add_break_info($report_id, $minutes){
		$data = array("rast_minuter" => $minutes);
		$this->db->where('id',$report_id);
		return $this->db->update('arbets_rapport', $data);
	}

	//returnerar en PHPExcel-fil, från tabellen arbets_rapport
	//todo: gör summa-cellen med excel egna summa-funktion
	//TODO: ta bort kolumn för id 
	public function make_excel($id=FALSE, $from=FALSE, $to=FALSE, $fliknamn = "Arbets_rapporter"){
		error_log("model make_excel, id $id, from $from, to $to, fliknamn $fliknamn");
		/*$this->load->model("taff_model");//för company info*/
		$this->load->model("user_model");//för log-funktion
		$this->load->model("company_model");
		$this->load->helper('url');

		$message = "";
		$letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";//obs max 26 kolumner
		$min_cell_width = 15;


		$fliknamn = substr($fliknamn, 0, 31);
		//from = $this->input->post("from");
		
		$tidsspann_text = "";

		$reports = array();
		if($id===FALSE || $id === NULL || $id == NULL || $id == "NULL"){
			error_log("id är null");
			if($from===FALSE && $to === FALSE){
				error_log("from/to är false");
				$reports = $this->get_reports_view_3();//returnerar array
			}
			else{
				error_log("from/to finns: $from/$to");
				$reports = $this->get_reports_view_3_date('',$from,$to);//todo test
				$tidsspann_text = "Tidsspann: $from - $to";
			}
		}
		else{
			error_log("id finns; $id");
			if($from===FALSE && $to === FALSE){
				error_log("from/to är false");
				$reports = $this->get_reports_view_3($id);
				//print_r($reports);
			}
			else{
				error_log("from/to finns: $from/$to");
				//echo "hämtar reports 4, id är $id";
				//if(empty($id))echo "empty<br>";
				//if(isset($id))echo "isset<br>";
				$reports = $this->get_reports_view_3_date($id,$from,$to);//todo test
				$tidsspann_text = "Tidsspann: $from - $to";
			}
		}

		//$reports är data med tidsrapporter: 
		/*
		 *Array
		 (
			 [id] => 104
			 [personal_id] => 20
			 [fornamn] => Valter
			 [efternamn] => Ekholm
			 [check_in_time] => 2019-03-06 08:40:11
			 [lati_in] => 59.289327
			 [longi_in] => 18.114237
			 [check_out_time] => 2019-03-06 12:11:53
			 [lati_out] => 59.289342
			 [longi_out] => 18.114223
			 [minuter] => 212
			 [benamning] =>
		 )

		 */

		if(empty($reports)){
			exit("Inget att skriva ut 277");
		}

		error_log(print_r($reports, true));

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

		/** Include PHPExcel */

		//company name
		$company = $this->company_model->get_company_by_id( $this->staff_model->get_agents_company_id());
		$company_name = $company["name"];
		error_log("Hämtade company_name $company_name");

		require_once 'PHPExcel/Classes/PHPExcel.php';

		$objPHPExcel = new PHPExcel();
		error_log("Skapade objekt");

		$objPHPExcel->getProperties()->setCreator($company_name)
			->setLastModifiedBy($company_name)
			->setTitle("reports for $company_name")
			->setSubject("reports for $company_name")
			->setDescription("Document, generated using PHP classes of PHPExcel.")
			->setKeywords("reports " . $company_name)
			->setCategory("Tidsrapporter");
		$len = sizeof($reports);

		error_log("Har satt properties");

		//ta ut kolumn-rubriker
		$row1 = $reports[0];//$result->fetch_array(MYSQLI_ASSOC);
		$keys = array_keys($row1);

		$cou=0;
		foreach($keys as $key){
			$txt = $letters[$cou]."1";
			//error_log("$cou Tar ut rubrik, $key");
			//$this->user_model->write_log("Ska sätta värde $key i cell $txt");
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($txt, $key);

			//cellbredd
			$col_name = $letters[$cou];
			$objPHPExcel->getActiveSheet()->getColumnDimension($col_name)->setWidth($min_cell_width);

			$cou++;
		}
		//error_log("Tog ut $cou rubriker");
		//ta ut kolumn-rubriker slut

		$count=2;//excel rad 2...
		$count__ = 0;//kolumn...
		$minutes = 0;
		foreach($reports as $r) {
			//error_log("En rad rapport");

			$lengths = array();

			$count__ = 0;
			foreach($keys as $key){
				$txt = $letters[$count__].$count;

				$value = $r[$key];
				//$this->user_model->write_log("Ska skriva värde i $txt : $value");

				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($txt, $value);
				$count__++;
			}
			//räkna tid
			$minutes += $r["minuter"];
			$count++;
		}//för varje rad $r
		//error_log("Klarade $count rader");
		//gör summa-rad

		$txt = $letters[$count__-2].$count;//längst från startsidan (ofta vänster)
		$value = "Summa timmar: ";
		//$this->user_model->write_log("Ska sätta timmar i $txt");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($txt, $value);

		$txt = $letters[$count__-1].$count;
		$value = $minutes/60;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($txt, $value);
		//error_log("Räknade minuter, $value");

		//todo: gör så excel räknar ut detta?
		
		//Print date limit for request
		if(isset($tidsspann_text)){
			 $txt = $letters[$count__-2].($count+1);
			 $value = $tidsspann_text;
			 $objPHPExcel->setActiveSheetIndex(0)->setCellValue($txt, $value);
		}

		$objPHPExcel->getActiveSheet()->setTitle($fliknamn);//obs max 31 tecken

		//so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		// Save Excel 2007 file
		//echo date('H:i:s') , " Write to Excel2007 format" , EOL;
		$callStartTime = microtime(true);
		//error_log("callStartTime $callStartTime");

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		$name_end = '_' . base64_decode($_SESSION["user_id"]) . '.xlsx';//_20.xlsx, om user är 20
		//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
		$name = str_replace('.php', $name_end, __FILE__);//byt ut .php mot _20.xlsx, förut med basename(__FILE__)
		error_log("Ska spara med namn $name");
		$objWriter->save($name);
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;

		$message .=  date('H:i:s') . " File written to " . str_replace('.php', $name_end, pathinfo(__FILE__, PATHINFO_BASENAME)) . EOL;

		$message .=  'Files have been created in ' . dirname(__FILE__) . EOL;
		$message .= "<a href='../models/reports/" . str_replace('.php', $name_end, pathinfo(__FILE__, PATHINFO_BASENAME)) . "'>Ladda ner filen</a><br>";
		//error_log("(model make_excel)" . $message);
		return $objPHPExcel;
	}

	public function make_pdf($id=FALSE, $from=FALSE, $to=FALSE, $fliknamn = "Arbets_rapporter"){

		error_log("model make_pdf, id $id, from $from, to $to, fliknamn $fliknamn");
		/*$this->load->model("taff_model");//för company info*/
		$this->load->model("user_model");//för log-funktion
		$this->load->model("company_model");
		$this->load->helper('url');

		$message = "";
		$letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";//obs max 26 kolumner
		$min_cell_width = 15;


		$fliknamn = substr($fliknamn, 0, 31);

		$reports = array();
		if($id===FALSE || $id === NULL || $id == NULL || $id == "NULL"){
			error_log("id är null");
			if($from===FALSE && $to === FALSE){
				error_log("from/to är false");
				$reports = $this->get_reports_view_3();//returnerar array
			}
			else{
				error_log("from/to finns: $from/$to");
				$reports = $this->get_reports_view_3_date('',$from,$to);//todo test
			}
		}
		else{
			error_log("id finns; $id");
			if($from===FALSE && $to === FALSE){
				error_log("from/to är false");
				$reports = $this->get_reports_view_3($id);
			}
			else{
				error_log("from/to finns: $from/$to");
				$reports = $this->get_reports_view_3_date($id,$from,$to);//todo test
			}
		}

		//$reports är data med tidsrapporter: 
		/*
		 *Array
		 (
			 [id] => 104
			 [personal_id] => 20
			 [fornamn] => Valter
			 [efternamn] => Ekholm
			 [check_in_time] => 2019-03-06 08:40:11
			 [lati_in] => 59.289327
			 [longi_in] => 18.114237
			 [check_out_time] => 2019-03-06 12:11:53
			 [lati_out] => 59.289342
			 [longi_out] => 18.114223
			 [minuter] => 212
			 [benamning] =>
		 )

		 */

		if(empty($reports)){
			exit("Inget att skriva ut 277");
		}

		//error_log(print_r($reports, true));

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');//?

		/** Include PHPExcel */

		//company name
		$company = $this->company_model->get_company_by_id( $this->staff_model->get_agents_company_id());
		$company_name = $company["name"];

		$search = array('å','ä','ö','Å','Ä','Ö');
		$replace = array('&aring;','&auml;','&ouml;','&Aring','&Auml;','&Ouml;');
		//make html text
		$html = str_replace($search, $replace, "<html><head><meta charset='UTF-8'><style>th{border-top: 1px solid gray; padding: 2px;}td{border: 1px solid gray; padding: 2px}</style></head><body><p>Företag: $company_name</p><p>Dokument skrivet datum: " . date("Y-m-d") . "</p>");

		$table = str_replace($search, $replace, "<table><tr><th>Id</th><th>Person-Id</th><th>Förnamn</th><th>Efternamn</th><th>Instämpling tid</th><th>Instämpling lat.</th><th>Instämpling long.</th><th>Utstämpling tid</th><th>Instämpling lat.</th><th>Instämpling long.</th><th>Minuter</th><th>Benämning</th></tr>");

		foreach($reports as $rep){
			//error_log("rep: " . print_r($rep, true));
			$ben = empty($rep["benamning"]) ? "-----" : $rep["benamning"];

			$table .= str_replace($search, $replace, "<tr><td>".$rep["id"].
				"</td><td>".$rep["personal_id"].
				"</td><td>".$rep["fornamn"].
				"</td><td>".$rep["efternamn"].
				"</td><td>".$rep["check_in_time"].
				"</td><td>".$rep["lati_in"].
				"</td><td>".$rep["longi_in"].
				"</td><td>".$rep["check_out_time"].
				"</td><td>".$rep["lati_out"].
				"</td><td>".$rep["longi_out"].
				"</td><td>".$rep["minuter"].
				"</td><td>$ben".
				"</td></tr>");
		}

		$table .= "</table>";

		$html .= $table;

		$html .= "</body></html>";

		$user = base64_decode($_SESSION["user_id"]);

		$filename = "reports_by_$user";
		$full_filename = "/var/www/example.com/public_html/jobb_rapport/application/models/" . $filename;//TODO: use __FILE__
		error_log("Ska spara $full_filename.html");
		if( !file_put_contents("$full_filename.html", $html, LOCK_EX)){//TODO: check if needed 3:rd param LOCK_EX
			error_log("Could not save html");
			exit("nohtml");
		}

		//system
		error_log("Ska anropa system med xhtml2pdf $filename.html");
		usleep(500);
		$res = system("xhtml2pdf -d $filename.html");

		error_log("Result: $res");

		if(file_exists("$full_filename.pdf")){
			error_log("Filen $full_filename.pdf fanns");
			return $full_filename.".pdf";
		}
		else{
			error_log("Filen $full_filename.pdf fanns inte");
			return false;
		}

	}

	public function get_work_length($report_id){
		$this->user_model->write_log("model get_work_length med $report_id");
		$sql = "select ceil(TIMESTAMPDIFF(second,check_in_time,check_out_time)/60) as minuter
		from arbets_rapport
		where arbets_rapport.id = $report_id";
		$query = $this->db->query($sql);//TODO: felkontroll, om fel return 0
		$row = $query->row();//one row only
		if(isset($row)){
			return $row->minuter;
		}
		else{
			return 0;
		}
		//$this->user_model->write_log("Resultat: " . print_r($query, true));
		//return $query->result_array();
	}

}
