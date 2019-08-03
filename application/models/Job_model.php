<?php
class Job_model extends CI_Model {//todo: testa uppdatera arbetstillfälle

	public function __construct()
	{
		$this->load->database();
	}

	public function create(){

	}

	//Hämtar jobb, utan anknyten data
	public function get_jobs($id=FALSE){
		error_log("get_jobs med  $id");
		if($id === FALSE){
			$query = $this->db->get('arbets_tillfalle');
			return $query->result_array();
		}

		$query = $this->db->get_where('arbets_tillfalle', array('id' => $id));
		return $query->row_array();
	}

	//hämtar arbetstillfälle med kopplad arbetsplats namn...
	//ger arbetstillälle (id,datum_start/slut, beskrivning) samt arbetsplats (id, namn)
	//argument $id är id på arbets_tillfälle
	public function get_jobs_view_1($id=FALSE){
		//echo "get_staff med " . $id . "<br>";
		if($id === FALSE){
			$query = $this->db->query("select arbets_tillfalle.id id, arbetsplats.id arb_pl_id, arbetsplats.namn arb_plats, datum_start, datum_slut, beskrivning, schema_id from arbetsplats right join arbets_tillfalle on (arbetsplats.id = arbetsplats_id)");
			return $query->result_array();
		}

		$query = $this->db->query("select arbets_tillfalle.id id, arbetsplats.id arb_pl_id, arbetsplats.namn arb_plats, datum_start, datum_slut, beskrivning, schema_id from arbetsplats right join arbets_tillfalle on (arbetsplats.id = arbetsplats_id) WHERE arbets_tillfalle.id = '$id'");
		return $query->row_array();
	}
	//

	//hämtar arbetstillfälle och räknar kopplad personals antal
	//argument: id för arbetstillfälle
	//ger arbetstillälle (id,datum_start/slut, beskrivning) samt arbetsplats (id, namn)
	public function get_jobs_view_2($id=FALSE){
		//echo "get_jobs_view_2 med " . $id . "<br>";
		if($id === FALSE){
			$sql = "select at.id, arbetsplats.namn as \"arbetsplats-namn\", at.datum_start, at.datum_slut, beskrivning, (select count(*) from personal_arbetstillfalle where id_arbetstillfalle = at.id) as antal_personal_anknyten from arbets_tillfalle at left join arbetsplats on (arbetsplats_id = arbetsplats.id) order by at.datum_start";
		}
		else{
			$sql = "select arbets_tillfalle.*, (select count(*) from personal_arbetstillfalle where id_arbetstillfalle = arbets_tillfalle.id) as antal_personal_anknyten from arbets_tillfalle where id='$id' order by datum_start";
		}
		error_log($sql);
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function get_jobs_view_2_has_schema(){
		//echo "get_jobs_view_2 med " . $id . "<br>";
		$sql = "select at.id, arbetsplats.namn as 'arbetsplats-namn', at.datum_start, at.datum_slut, beskrivning, (select count(*) from personal_arbetstillfalle where id_arbetstillfalle = at.id) as antal_personal_anknyten, if(schema_id is null,'nej','ja') as 'har schema' from arbets_tillfalle at left join arbetsplats on (arbetsplats_id = arbetsplats.id) order by at.datum_start;";
		//error_log($sql);
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_jobs_view_2_dates($from, $to){
		//echo "get_jobs_view_2 med " . $id . "<br>";
		$sql = "select at.id, arbetsplats.namn as \"arbetsplats-namn\", at.datum_start, at.datum_slut, beskrivning, (select count(*) from personal_arbetstillfalle where id_arbetstillfalle = at.id) as antal_personal_anknyten from arbets_tillfalle at left join arbetsplats on (arbetsplats_id = arbetsplats.id) WHERE at.datum_start >= '$from' AND at.datum_slut< '$to' order by at.datum_start";
		error_log($sql);
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_jobs_for_person($id=FALSE){
		if($id === FALSE){
			exit();
		}
		$sql = "select at.id from arbets_tillfalle at left join personal_arbetstillfalle on(id=id_arbetstillfalle) where id_person=$id";

		//NOT READY

		$query = $this->db->query($sql);
		return $query->result_array();
	}	


	public function get_jobs_for_comp($id){
		error_log("get_jobs_for_comp ($id)...");

		$sql = "SELECT DISTINCT at.id,ap.namn AS \"arbetsplats-namn\",longi,lati,gatu_adress,postnummer,trappor,datum_start,datum_slut,beskrivning, (SELECT COUNT(*) FROM personal_arbetstillfalle WHERE id_arbetstillfalle = at.id) AS antal_personal_anknyten FROM companies_kunder ck LEFT JOIN kund k ON (id_kund = k.id) LEFT JOIN kunder_arbetsplatser ka ON (k.id = ka.id_kund) LEFT JOIN arbetsplats ap ON (id_arbetsplats = ap.id) JOIN arbets_tillfalle at ON (ap.id = at.arbetsplats_id) WHERE id_company = $id";

		error_log($sql);

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_job_schema_ids_for_company($id){
		error_log("get_job_schema_ids_for_company ($id)");

		$sql = "SELECT DISTINCT at.schema_id FROM companies_kunder ck JOIN kund k ON (id_kund = k.id) JOIN kunder_arbetsplatser ka ON (k.id = ka.id_kund) JOIN arbetsplats ap ON (id_arbetsplats = ap.id) JOIN arbets_tillfalle at ON (ap.id = at.arbetsplats_id) WHERE id_company = $id AND schema_id IS NOT NULL";

		error_log($sql);

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_repetitions_dates($from, $to){
		/* all jobs that has schema, it's repetitions, within time frame */
		$sql = "SELECT * FROM arbets_tillfalle WHERE datum_slut < '$to'";
		$query = $this->db->query($sql);
		$jobs = $query->result_array();
		$jobs_with_schema = array();
		foreach($jobs as $job){
			if(!empty($job["schema_id"])){
				$jobs_with_schema[] = $job;
			}
		}
		if(empty($jobs_with_schema)){
			error_log("No job with schema");
			return null;
		}

		$repetitions = array();
		foreach($jobs_with_schema as $job){
			$repetitions[$job["id"]] =
				$this->schema_model->get_events_from_job_dateframe($job, $from, $to);
			/*should not include original date*/
		}
		return $repetitions;

	}

	public function get_jobs_for_comp_dates($id, $from, $to){
		error_log("get_jobs_for_comp_dates (id: $id, from: $from, to: $to)");

		$sql = "SELECT DISTINCT at.id, ap.namn AS \"arbetsplats-namn\",longi,lati,gatu_adress,postnummer,trappor,datum_start,datum_slut,beskrivning, (SELECT COUNT(*) FROM personal_arbetstillfalle WHERE id_arbetstillfalle = at.id) AS antal_personal_anknyten FROM companies_kunder ck LEFT JOIN kund k ON (id_kund = k.id) LEFT JOIN kunder_arbetsplatser ka ON (k.id = ka.id_kund) LEFT JOIN arbetsplats ap ON (id_arbetsplats = ap.id) JOIN arbets_tillfalle at ON (ap.id = at.arbetsplats_id) WHERE id_company = $id AND at.datum_start >= '$from' AND at.datum_slut < '$to'";

		//error_log($sql);

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_repetitions_for_comp_dates($id, $from, $to){
		/* all jobs for a company, that has schema, it's repetitions, within time frame */
		error_log("get_repetitions_for_comp_dates $id, $from, $to");
		$this->load->model("schema_model");
		$sql = "SELECT DISTINCT at.* FROM companies_kunder ck LEFT JOIN kund k ON (id_kund = k.id) LEFT JOIN kunder_arbetsplatser ka ON (k.id = ka.id_kund) LEFT JOIN arbetsplats ap ON (id_arbetsplats = ap.id) JOIN arbets_tillfalle at ON (ap.id = at.arbetsplats_id) WHERE id_company = $id AND at.datum_slut < '$to'";

		$query = $this->db->query($sql);
		$jobs = $query->result_array();
		$jobs_with_schema = array();

		foreach($jobs as $job){
			if(!empty($job["schema_id"])){
				$jobs_with_schema[] = $job;
			}
		}
		if(empty($jobs_with_schema)){
			error_log("No job with schema");
			return null;
		}

		$repetitions = array();
		foreach($jobs_with_schema as $job){
			$repetitions[$job["id"]] = $this->schema_model->get_events_from_job_dateframe($job, $from, $to);
		}
		return $repetitions;
	}


	public function get_jobs_for_company($id){
		error_log("get_jobs_for_company med $id");
		$sql = "SELECT at.id,ap.namn as arb_plats,longi,lati,gatu_adress,postnummer,trappor,datum_start,datum_slut,beskrivning, (select count(*) from personal_arbetstillfalle where id_arbetstillfalle = at.id) as antal_personal_anknyten FROM companies_kunder ck left join kund k on (id_kund = k.id) left join kunder_arbetsplatser ka ON (k.id = ka.id_kund) left join arbetsplats ap ON (id_arbetsplats = ap.id) left join arbets_tillfalle at ON (ap.id = at.arbetsplats_id) WHERE id_company = $id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function is_job_for_company($id_comp, $id_job){
		$sql = "SELECT COUNT(at.id) AS COUNTED FROM companies_kunder ck left join kund k on (id_kund = k.id) left join kunder_arbetsplatser ka ON (k.id = ka.id_kund) left join arbetsplats ap ON (id_arbetsplats = ap.id) left join arbets_tillfalle at ON (ap.id = at.arbetsplats_id) WHERE id_company = $id_comp AND at.id = $id_job";
		error_log($sql);
		$query = $this->db->query($sql);
		$row =  $query->row();//get first row
		return $row->COUNTED > 0;
	}

	public function get_staffmembers_jobs($staffmember){
		error_log("get_staffmembers_jobs($staffmember)");//TODO: testa anropande kod, då \"arbetsplats-namn\" är nytt
		$sql = "SELECT at.id, ap.namn as \"arbetsplats-namn\",longi,lati,".
			"gatu_adress,postnummer,trappor,datum_start,".
			"datum_slut,beskrivning FROM personal p ".
			"LEFT JOIN personal_arbetstillfalle pa ".
			"ON (p.id = id_person) ".
			"LEFT JOIN arbets_tillfalle at ".
			"ON (id_arbetstillfalle = at.id) ".
			"JOIN arbetsplats ap ON (arbetsplats_id = ap.id) ".
			"WHERE p.id = $staffmember";

		error_log($sql);

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function get_staffmembers_jobs_dates($staffmember, $from, $to){
		error_log("get_staffmembers_jobs($staffmember)");//TODO: testa anropande kod, då \"arbetsplats-namn\" är nytt
		$sql = "SELECT at.id, ap.namn as \"arbetsplats-namn\",longi,lati,".
			"gatu_adress,postnummer,trappor,datum_start,".
			"datum_slut,beskrivning FROM personal p ".
			"LEFT JOIN personal_arbetstillfalle pa ".
			"ON (p.id = id_person) ".
			"LEFT JOIN arbets_tillfalle at ".
			"ON (id_arbetstillfalle = at.id) ".
			"JOIN arbetsplats ap ON (arbetsplats_id = ap.id) ".
			"WHERE p.id = $staffmember AND at.datum_start >= '$from'" . 
			"AND at.datum_slut < '$to'";

		error_log($sql);

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/*
	 * $staffmember - id of staff
	 * $from - date as string
	 * $to - date as string
	 *
	 * returns array with for each job with schema: Array
	 *         (
	[0] => Array(
	[arbetstillfalle_id] => 28
	*[datum_start] => 2019-06-19 21:30:00
	*[datum_slut] => 2019-06-19 22:30:00
	*)
	 * */
	public function get_staffmembers_repetitions_dates($staffmember, $from, $to){
		error_log("get_staffmembers_repetitions_dates");
		$this->load->model("schema_model");

		$sql1 = "SELECT at.* FROM personal p ".
			"LEFT JOIN personal_arbetstillfalle pa ".
			"ON (p.id = id_person) ".
			"LEFT JOIN arbets_tillfalle at ".
			"ON (id_arbetstillfalle = at.id) ".
			"JOIN arbetsplats ap ON (arbetsplats_id = ap.id) ".
			"WHERE p.id = $staffmember AND at.datum_slut < '$to'";
		$query = $this->db->query($sql1);
		$jobs = $query->result_array();/*got his/her jobs*/

		$jobs_with_schema = array();

		foreach($jobs as $job){
			if(!empty($job["schema_id"])){
				//error_log("found job with schema");
				$jobs_with_schema[] = $job;
			}
		}
		if(empty($jobs_with_schema)){
			return null;
		}

		$repetitions = array();
		foreach($jobs_with_schema as $job){
			$repetitions[$job["id"]] = $this->schema_model->get_events_from_job_dateframe($job, $from, $to);
		}//a job can only have one schema
		return $repetitions;
	}

	/*
	Start-datum
	Slut-datum
	Arbetsplats-namn
	Latitud
	Longitud
	 */
	//todo: fråga om Kund-namn behövs
	//Returnerar arbetstillfällen med lati- och longitud start- och slutdatum, namn på arbetsplats samt hur många personer som är anknytna
	//argument: id - arbetstillfälle id
	public function get_jobs_view_3($id=FALSE){
		if($id===FALSE){
			$query = $this->db->query("SELECT namn, gatu_adress, trappor, postnummer, lati, longi, at.datum_start datum_start, at.datum_slut datum_slut, (select count(*) FROM personal_arbetstillfalle WHERE id_arbetstillfalle = arbets_tillfalle.id) AS antal_personal_anknyten FROM arbets_tillfalle at LEFT JOIN personal_arbetstillfalle ON (arbets_tillfalle.id = id_arbetstillfalle) LEFT JOIN arbetsplats ON (arbetsplats.id = arbetsplats_id)");
			if ($query)
				return $query->result_array();
			else return false;
		}

		$query = $this->db->query("select namn, gatu_adress, trappor, postnummer, lati, longi, datum_start, datum_slut, beskrivning, (select count(*) from personal_arbetstillfalle where id_arbetstillfalle = arbets_tillfalle.id) as antal_personal_anknyten from arbets_tillfalle left join personal_arbetstillfalle on (arbets_tillfalle.id = id_arbetstillfalle) left join arbetsplats on (arbetsplats.id = arbetsplats_id) where arbets_tillfalle.id=$id");
		if($query)
			return $query->row_array();
		else return false;
	}


	//jobb för en person, se staff_model och get_jobs_for_person


	/*
	query för även kund-namn
	select arbetsplats.namn as arbetsplats, kund.namn as kund,
	lati, longi, arbets_tillfalle.*,
	(select count(*) from personal_arbetstillfalle where id_arbetstillfalle = arbets_tillfalle.id)
	as "antal personal anknyten"
	from arbets_tillfalle left join personal_arbetstillfalle on (arbets_tillfalle.id = id_arbetstillfalle)
	left join arbetsplats on (arbetsplats.id = arbetsplats_id)
	left join kunder_arbetsplatser on (arbetsplats.id = id_arbetsplats)
	left join kund on (id_kund = kund.id);
	 */

	public function get_jobs_for_dropdown(){
		$level = $this->user_model->get_level();

		if($level == 1){
			$sql = "SELECT id, beskrivning, datum_start FROM arbets_tillfalle";
		}
		else if($level == 2){

			$company_id = $this->staff_model->get_staffmembers_company_id(base64_decode($_SESSION["user_id"]));

			$sql = "SELECT DISTINCT at.id, at.beskrivning, at.datum_start FROM companies_kunder ck LEFT JOIN kund k ON (id_kund = k.id) LEFT JOIN kunder_arbetsplatser ka ON (k.id = ka.id_kund) LEFT JOIN arbetsplats ap ON (id_arbetsplats = ap.id) JOIN arbets_tillfalle at ON (ap.id = at.arbetsplats_id) WHERE id_company = $company_id";
			//error_log($sql);

		}
		else{
			return;
		}


		$query = $this->db->query($sql);
		$result = array();                                                                                     if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$datum = $row->datum_start;
				$datum = substr($datum, 0, 11);
				$result[$row->id] = $row->beskrivning . " --- " . $datum;
			}
			return $result;
		}
		else return null;

	}

	public function get_related_schema($schema_id){
		error_log("get_related_schema($schema_id)");
		$sql = "SELECT * FROM jobb_schema WHERE id = $schema_id";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	public function unset_schema($job_id){
		error_log("unset_schema($job_id)");
		$sql = "UPDATE arbets_tillfalle SET schema_id = null WHERE id = $job_id";
		error_log($sql);
		$query = $this->db->query($sql);
		return $this->db->affected_rows() == 1;
	}

	public function set_job()
	{
	    /*
| id             | int(10)   | NO   | PRI | NULL    | auto_increment |
| arbetsplats_id | int(10)   | YES  |     | NULL    |                |
| datum_start    | timestamp | YES  |     | NULL    |                |
| datum_slut     | timestamp | YES  |     | NULL    |                |
| beskrivning    | text      | YES  |     | NULL    |                |
	     */
		$this->load->helper('url');
		$data = array(
			'arbetsplats_id' => $this->input->post('workplace'),
			'datum_start' => $this->input->post('start'),
			'datum_slut' => $this->input->post('end'),
			'beskrivning' => $this->input->post('description')
		);
		$result = $this->db->insert('arbets_tillfalle', $data);

		$last_id = $this->db->insert_id();

		if($result) return $last_id;
		else return false;

	}

	public function set_job_field($field, $value, $id){
		error_log("set_job_field($field, $value, $id)");
		$data = array(
			"$field" => $value
		);
		error_log(print_r($data, true));
		$this->db->where('id', $id);
		$result = $this->db->update('arbets_tillfalle', $data);
		return $result;
	}

	public function update_job(){
		error_log("job_model Update job...");
		$this->load->helper('url');
		$id = $this->input->post('id');
		$data = array(
			'arbetsplats_id' => $this->input->post('workplace'),
			'datum_start' => $this->input->post('start'),
			'datum_slut' => $this->input->post('end'),
			'beskrivning' => $this->input->post('description')
		);
		//error_log("Ska uppdatera med data " . print_r($data, true));
		$this->db->where('id', $id);
		$result = $this->db->update('arbets_tillfalle',$data);
		//error_log($this->db->last_query());
		return $result;
	}

	public function delete_job($id){
		$this->user_model->write_log("job_model Delete job $id");
		$this->db->where('id',$id);
		return $this->db->delete('arbets_tillfalle');
	}

	//hämta personal som anknutits till det aktuella arbetstillfället
	public function get_staff($id=FALSE){
		if($id!==FALSE){
			$query = $this->db->query("select id_person,id_arbetstillfalle,id,fornamn,efternamn,email,tel,personnummer,level,company_id,created_date from personal_arbetstillfalle right join personal on (id_person = id) where (id_arbetstillfalle = '$id')");
			return $query->result_array();
		}
	}

	public function get_staff_clean($id_arbetstillfalle=FALSE){
		if($id_arbetstillfalle!==FALSE){
			$query = $this->db->query("select p.* from personal_arbetstillfalle right join personal p on (id_person = id) where (id_arbetstillfalle = '$id_arbetstillfalle')");
			return $query->result_array();
		}
	}
	
	public function get_staff_public($id_arbetstillfalle=FALSE){
		if($id_arbetstillfalle!==FALSE){
			$query = $this->db->query("select p.fornamn, p.efternamn, p.email, p.tel from personal_arbetstillfalle right join personal p on (id_person = id) where (id_arbetstillfalle = '$id_arbetstillfalle')");
			return $query->result_array();
		}
	}


	//hämtar poster för den personal som enligt tabellen personal_arbetstillfalle ej är kopplad till aktuellt arbetstillfälle
	public function get_unrelated_staff($id=FALSE){
		if($id!==FALSE){
			$query = $this->db->query("select * from personal_arbetstillfalle right join personal on (id_person = id) where (id_arbetstillfalle != '$id' or isnull(id_arbetstillfalle))");

			return $query->result_array();
		}
	}

	public function get_unrelated_staff_company($id=FALSE, $company){
		error_log("get_unrelated_staff_company($id, $company)");
		if($id!==FALSE){
			$sql = "select * from personal where id not in (select id_person from personal_arbetstillfalle where id_arbetstillfalle = $id) and company_id = $company";
			$query = $this->db->query($sql);
			return $query->result_array();
		}
	}

	public function get_staff_link_table($comp_id = FALSE){
		if( $comp_id == FALSE){
			error_log("get_staff_link_table($comp_id)");
			$query = $this->db->get('personal_arbetstillfalle');
			//error_log(print_r($query->result_array()), true);
		}
		else{
			$staff = $this->staff_model->get_staff_by_company($comp_id);
			$right_staff = "";
			foreach ($staff as $sm){/*build a string of id's*/
				$right_staff .= $sm["id"] .",";
			}
			$right_staff = rtrim($right_staff, ",");
			$query = $this->db->query("SELECT * FROM personal_arbetstillfalle WHERE id_person IN ($right_staff)");
		}

		return $query->result_array();
	}

	public function get_staff_link_table_nullcheck(){//TODO: varför uppstår glapp tomma fält?
		error_log("get_staff_link_table_nullcheck");
		$query = $this->db->query('select personal_arbetstillfalle.*, id from personal_arbetstillfalle left join arbets_tillfalle on (id_arbetstillfalle = id)');
		return $query->result_array();
	}
	public function delete_empty_links_from_links_table(){
		$sql = "delete personal_arbetstillfalle from personal_arbetstillfalle left join arbets_tillfalle on (id_arbetstillfalle = id) where id is null";
		$this->db->query($sql);
		return $this->db->affected_rows();
	}

	//lägger ett par id:n i tabell personal_arbetstillfalle som anger en relation mellan ett arbetstillfälle o en personal
	public function add_staff(){
		$this->load->helper('url');
		$data = array(
			'id_arbetstillfalle' => $this->input->post('job'),
			'id_person' => $this->input->post('person')
		);
		$res = $this->db->insert('personal_arbetstillfalle', $data);
		if($res){

			return $this->db->affected_rows();
		}
		error_log("add_staff result: $res");
		$db_error = $this->db->error();
		if (!empty($db_error)) {
			error_log("FEL - kunde inte anknyta person; " . print_r($db_error, true));
			return $db_error["code"];
		}
	}

	public function delete_connection_record($id_job,$id_staff){
		$this->user_model->write_log("I job model delete_connection_record med $id_job, $id_staff");
		$array = array('id_arbetstillfalle'=>$id_job, 'id_person'=>$id_staff);
		$this->db->where($array);
		return $this->db->delete('personal_arbetstillfalle');
	}

	/*calendar functions*/
	function getStartAndEndDateOfWeekAndYear($week, $year) {
		$dto = new DateTime();
		$dto->setISODate($year, $week);
		$ret['week_start'] = $dto->format('Y-m-d');
		$dto->modify('+6 days');
		$ret['week_end'] = $dto->format('Y-m-d');
		return $ret;
	}
}
?>
