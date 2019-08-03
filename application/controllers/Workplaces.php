<?php
class Workplaces extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('workplace_model');
		$this->load->model('user_model');
		$this->load->model('company_model');//to get controlled_mode
		$this->load->helper('url_helper');
		$this->load->helper('screen_out_helper');

		$company = $this->staff_model->get_agents_company();
		if($company !== null){
		$this->company_name = $company["name"];
		$this->street = $company["gatuadress"];
		$this->postal_code = $company["postnummer"];
		$this->email_ = $company["email"];
		$this->phone = $company["telefon"];
		$this->cctld = $company["cctld"];
		$this->controlled_mode = $this->company_model->get_controlled_mode($company["id"]);
		}
		else{
			$this->company_name = "-";
			$this->street = "-";
			$this->postal_code = "-";
			$this->email_ = "-";
			$this->phone = "-";
			$this->cctld = "-";
			$this->controlled_mode = 0;//false
		}

	}

	public function index($id=FALSE)
	{//TODO: when used? When click link in edit page
		redirect('/workplaces/view2');
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";

		if($id=== FALSE){
			error_log("id is false");
			$data['workplaces'] = $this->workplace_model->get_workplaces();
			$data['title'] = 'Alla arbetsplatser, ' . $this->company_name;
			header_view_footer('workplace/index', $data);
		}
		else if(is_numeric($id)){
			error_log("id is numeric");
			$this->load->model('customer_model');
			$data['workplace'] = $this->workplace_model->get_workplace($id);
			$data['customer'] = $this->customer_model->get_customers_by_id($data['workplace']['id']);
			$data['title'] = 'Arbetsplats hos ' . $data["customer"]["namn"];
			header_view_footer('workplace/card', $data);
		}

	}

	//in use
	public function view2($id=FALSE)
	{
		error_log("view2 med id $id");
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";
		$data["head_ext_script"] = "<script src=\"".base_url()."js/Script.js\" />";
		if($id=== FALSE){
			//is admin 1?
			$level = $this->user_model->get_level();
			
			$data['title'] = 'Alla arbetsplatser'; 
			if($level == 1){
				$data['workplaces'] = $this->workplace_model->get_workplaces_view2();
			}
			else if($level == 2){
				$data['workplaces'] = $this->workplace_model->get_workplaces_view2_company($this->staff_model->get_agents_company_id());
				$data['title'] .= ", $this->company_name";
			}
			else if ($level == 3){
				//TODO: get_workplaces_by_staff_member
			}
			//$data['workplaces'] = $this->workplace_model->get_workplaces_view2();
			header_view_footer('workplace/index', $data);
		}
		else if(is_numeric($id)){
			$this->load->model('customer_model');
			$data['workplace'] = $this->workplace_model->get_workplace($id);
			$data['customer'] = $this->customer_model->get_customers_by_id($data['workplace']['id']);
			$data['title'] = 'Arbetsplats hos ' . $data["customer"]["namn"];
			header_view_footer('workplace/card', $data);
		}

	}

	public function create()
	{//AIzaSyC29vY3h6pTA1XeLNWDsk_2fTxHMutgnhs google geocode api key
		//https://maps.googleapis.com/maps/api/geocode/json?address=Ringvagen+Stockholm&key=AIzaSyC29vY3h6pTA1XeLNWDsk_2fTxHMutgnhs

		error_log("Controller Workplace create");
		$this->load->model('customer_model');

		$this->load->helper('form');
		$this->load->library('form_validation');

		error_log("controlled_mode = " . $this->controlled_mode);
		//informative table
		$data["chain_table"] = $this->controlled_mode == 1 ? get_table_chain("arbetsplats") : ""; 

		$data["head_ext_script"] = "<script type=\"text/javascript\" src=\"".
			base_url()."js/Script.js\" ></script>";
		$data['head_ext_script'] .= '<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==" crossorigin=""></script>';

		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\""
			.base_url()."css/style.css\" />";
		$data["head_ext_css"] .=  '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>';

		$data['title'] = 'Spara ny arbetsplats';
		$level = $this->user_model->get_level();

		if($level == 1){
			$data['customers'] = $this->customer_model->get_customers_by_id();
			$data["comp_time_zone"] = "0";

			$data['company'] = 0;
		}
		else if($level == 2){
			$data['customers'] = $this->customer_model->get_companys_customers($this->staff_model->get_agents_company_id());

			$data["comp_time_zone"] = $this->company_model->get_timezone($this->staff_model->get_agents_company_id());

			$data['company'] = $this->staff_model->get_agents_company_id();
		}

		$this->form_validation->set_rules('name', 'Namn', 'trim|required');
		$this->form_validation->set_rules('street', 'Gatu-adress', 'trim');
		$this->form_validation->set_rules('postal_code', 'Postnummer', 'trim');
		$this->form_validation->set_rules('stairs', 'Trappor', 'trim');
		$this->form_validation->set_rules('land', 'Land', 'trim');
		$this->form_validation->set_rules('lati', 'Latitud', 'trim');
		$this->form_validation->set_rules('longi', 'Longitud', 'trim');
		$this->form_validation->set_rules('time_zone', 'Tidszon', 'trim|max_length[3]');
		$this->form_validation->set_rules('customer', 'Kund-id',
			array(
				'required',
				array('customer_callable',
				function($str){
					$this->form_validation->set_message('customer_callable', 'Kund måste anges');
					return intval($str)>0;
				}
				)
			)
		);

		error_log(print_r($_POST, true));
		$cus = $this->input->post('customer');
		//error_log("Got cus: $cus");

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('workplace/create', $data);
		}
		else
		{
			$last_id = $this->workplace_model->set_workplace();
			if($last_id){
				error_log("Saved a workplace with id $last_id");
				//got id for new workplace

				$customer = $this->input->post('customer');
				error_log("Customer $customer");

				$this->workplace_model->add_customer($customer, $last_id);

				header_view_footer('workplace/success');
			}
			else{
				header_view_footer('pages/error');
			}
		}
	}

	public function edit($id){
		$id = base64_decode($id);
		$this->load->helper('form');
		$this->load->library('form_validation');

		$level = $this->user_model->get_level();


		$data['title'] = 'Redigera arbetsplats uppgifter';
		$data['workplace'] = $this->workplace_model->get_workplace($id);
        $data["chain_table"] = $this->controlled_mode == 1 ? get_table_chain("arbetsplats") : "";
        if($level == 1){
			$data['related_customers'] = $this->workplace_model->get_related_customers($id);
		}
		else if($level == 2){
			$data['related_customers'] = $this->workplace_model->get_related_customers_company($id, $this->staff_model->get_agents_company_id());
		}
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";
        $data["head_ext_css"] .=  '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>';
		$data["head_ext_script"] = "<script type=\"text/javascript\" src=\"".
			base_url()."js/Script.js\"></script>";
        $data['head_ext_script'] .= '<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==" crossorigin=""></script>';
		header_view_footer('workplace/edit', $data);
	}

	public function update(){
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Uppdatera en arbetsplats uppgifter';
		//test
		$form_data = $this->input->post();
		//print_r($form_data);
		if(isset($id)){//?
			$data['workplace'] = $this->workplace_model->get_workplace($id);
		}
		else{
			$data['workplace'] = $this->workplace_model->get_workplace($form_data["id"]);
		}
		$this->form_validation->set_rules('id', 'Id', 'trim|required');
		$this->form_validation->set_rules('name', 'Namn', 'trim|required');
		$this->form_validation->set_rules('street', 'Gatu-adress', 'trim');
		$this->form_validation->set_rules('postal_code', 'Postnummer', 'trim');
		$this->form_validation->set_rules('stairs', 'Trappor', 'trim');
		$this->form_validation->set_rules('land', 'Land', 'trim');
		$this->form_validation->set_rules('lati', 'Latitud', 'trim');
		$this->form_validation->set_rules('longi', 'Longitud', 'trim');
		$this->form_validation->set_rules('cust_id', 'Kund- arbetsplats -id', 'trim');
		$this->form_validation->set_rules('time_zone', 'Tidszon', 'trim|max_length[3]');	

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('workplace/edit', $data);
		}
		else
		{
			$this->workplace_model->update_workplace();
			header_view_footer('workplace/success');
		}
	}

	public function delete_row($id){
		$data['title'] = 'Delete a customer record';

		if(is_numeric($id)){
			if($this->workplace_model->delete_workplace_record($id)){
				header_view_footer('workplace/success');
			}
			else{
				echo "Gick inte";
			}
		}
	}

	public function delete_customer_connection($id_workplace,$id_customer, $view){
		$this->user_model->write_log("I delete_customer_connection");
		$workplace = base64_decode($id_workplace);
		$customer = base64_decode($id_customer);
		if($view === "wp"){
			$view = 'workplace/success';
		}
		else if($view === "c"){
			$view = 'customer/success';
		}
		else{
			$view = '/';
		}
		$data['title'] = 'Delete a workplace-customer association';

		if(is_numeric($workplace) && is_numeric($customer)){
			if($this->workplace_model->delete_connection_record($workplace,$customer)){
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

	public function connect_any_customer($id_workplace, $id_customer=FALSE){//id_customer används ej 29 aug 17
		$id_workplace = base64_decode($id_workplace);
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Anknyt kund';
		$level = $this->user_model->get_level();
		if(is_numeric($id_workplace)){
			if($level == 1){
				$data['workplace'] = $this->workplace_model->get_workplace($id_workplace);
		    	$data['unrelated_customers'] = $this->workplace_model->get_unrelated_customers($id_workplace);
			}
			else if($level == 2){
				//$data['workplace'] = $this->workplace_model->get_workplaces_for_company($this->staff_model->get_agents_company_id());
                $data['workplace'] = $this->workplace_model->get_workplace($id_workplace);
			    $data['unrelated_customers'] = $this->workplace_model->get_unrelated_customers_company($id_workplace, $this->staff_model->get_agents_company_id());
			}
			//print_r($data);
			$data['header'] = 'Anknyt kund till ' . $data["workplace"]["namn"];

			header_view_footer('workplace/edit_customers', $data);
		}
	}

	public function add_customer(){
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Koppla kund till arbetsplats';
		$data['header'] = "Koppla kund till arbetsplats";
		
		$this->form_validation->set_rules('workplace', 'Arbetsplats id', 'trim|required');
		$this->form_validation->set_rules('customer', 'Kund id', 'trim|required');

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('workplace/edit_customers', $data);
		}
		else
		{
			$this->workplace_model->add_customer();
			header_view_footer('workplace/success');
		}
	}

	public function view1(){
		$data['title'] = 'Se arbetsplatser med anknyten kund';
		$data['workplaces'] = $this->workplace_model->get_workplaces_view1();
		$data['head_ext_css'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."css/style.css\" />";
		header_view_footer('workplace/index', $data);
	}

	//for ajax
	//Not tested
	public function get_workplaces_for_dropdown($company){

		$cid = intval($company);
		if($cid>0){
			$cs = $this->workplace_model->get_workplaces_for_company($cid);
		}
		else if($cid == 0){
			$cs = $this->workplace_model->get_workplaces();
		}
		else{
			echo "0";
			return false;
		}

		$data = array();
		foreach($cs as $c){
			error_log(print_r($c, true));
		}

	}

	public function get_page(){
		//post data "url"
		$this->load->helper('form');
		$this->load->library('form_validation');
		$form_data = $this->input->post();
		$this->form_validation->set_rules('street', 'Gata', 'trim|required');
		$this->form_validation->set_rules('postal_code', 'Postnummer', 'trim');
		$this->form_validation->set_rules('city', 'Stad', 'trim');
		$data['title'] = 'Se position för adress';

		if ($this->form_validation->run() === FALSE)
		{
			$data['result'] = "";
			$data['info'] = "Fyll i en adress";
			$data["lat"] = FALSE;
			$data["lon"] = FALSE;
			$data['street'] = $street = "";
			$data['postal_code'] = $postal_code = "";
			$data['city'] = $city = "";
			header_view_footer('workplace/get_page', $data);
		}
		else{
			$street = $form_data["street"];
			$postal_code = $form_data["postal_code"];
			$city = $form_data["city"];
			//https://maps.googleapis.com/maps/api/geocode/json?address=Ringvagen+Stockholm&key=
			$url = "https://maps.googleapis.com/maps/api/geocode/";
			$key = "AIzaSyC29vY3h6pTA1XeLNWDsk_2fTxHMutgnhs";
			$format = "json";
			$address = urlencode($street);//." ".$city." ".$postal_code

			if(isset($city)){
				$address .= urlencode(" ".$city);
			}
			if(isset($postal_code)){
				//echo "(postal code $postal_code)";
				$address .= urlencode(" ".$postal_code);
			}
			$url_concat = $url.$format."?address=".$address."&key=".$key;
			$data['info'] = $url_concat;
			$data['result'] = htmlentities(file_get_contents($url_concat, "r"));

			$start = strstr($data['result'],"location");
			$pos = strpos($start,'{');
			$data['start'] = substr($start,$pos);

			$end_pos = strpos($data['start'],'}');

			$pos = substr($data['start'],0,$end_pos);

			$pos = strstr($pos,':');

			$lat_lon = preg_replace("/[^0-9,.]/", "", $pos);

			$lat = substr($lat_lon,0,strpos($lat_lon,','));

			$lon = str_replace(',','',substr($lat_lon,strpos($lat_lon,',')));

			$data['pos'] = $lat_lon;
			$data['lat'] = floatval($lat);
			$data['lon'] = floatval($lon);

			$data['street'] = $street;
			$data['postal_code'] = $postal_code;
			$data['city'] = $city;
			$data['json'] = "";//json_decode(utf8_encode($data['result']));

			$data['json_array'] = "";
			header_view_footer('workplace/get_page', $data);
		}

		header_view_footer('templates/footer');
	}

	//anropas med ajax
	//returnerar json-formaterad text
	public function get_coords_google(){
		error_log("get_coords");
		$this->load->helper('form');
		$this->load->library('form_validation');
		$form_data = $this->input->post();
		$this->form_validation->set_rules('street', 'Gata', 'trim|required');
		$this->form_validation->set_rules('postal_code', 'Postnummer', 'trim');
		$this->form_validation->set_rules('city', 'Stad', 'trim');
		$data['title'] = 'Se position för adress';

		if ($this->form_validation->run() === FALSE)
		{
			echo "Saknas gata";
		}
		else{
			$street = $form_data["street"];


			//https://maps.googleapis.com/maps/api/geocode/json?address=Ringvagen+Stockholm&key=
			$url = "https://maps.googleapis.com/maps/api/geocode/";
			$key = "AIzaSyC29vY3h6pTA1XeLNWDsk_2fTxHMutgnhs";/*API key-jobb*/
			$format = "json";
			$address = urlencode($street);

			if(isset($city)){
				$city = $form_data["city"];
				$address .= urlencode(" ".$city);
			}
			if(isset($postal_code)){
				$postal_code = $form_data["postal_code"];
				$address .= urlencode(" ".$postal_code);
			}
			$url_concat = $url.$format."?address=".$address."&key=".$key;
			$data['info'] = $url_concat;
			$result = file_get_contents($url_concat, "r");
			error_log($result);//TODO: använd json_decode
			$data['result'] = htmlentities($result);

			$start = strstr($data['result'],"location");
			$pos = strpos($start,'{');
			$data['start'] = substr($start,$pos);

			$end_pos = strpos($data['start'],'}');

			$pos = substr($data['start'],0,$end_pos);

			$pos = strstr($pos,':');

			$lat_lon = preg_replace("/[^0-9,.]/", "", $pos);


			$lat = substr($lat_lon,0,strpos($lat_lon,','));
			$lon = str_replace(',','',substr($lat_lon,strpos($lat_lon,',')));

			$center_of_map_lat = $lat;//59.319178;
			$center_of_map_long =$lon;//18.095856;
			$zoom_level = 14;
			$url_map = "https://kartor.eniro.se/?c=".$center_of_map_lat.",".$center_of_map_long."&z=".$zoom_level."&g=".$lat.",".$lon;

			$iframe = "$url_map<br><iframe width='100%' height='70%' src='$url_map' id='iframe1'></iframe>";

			$result ="{\"lat\":$lat,\"lon\":$lon,\"karta\":\"$iframe\"}";//json

			echo $result;

		}

	}

	//locationiq anropas med GET https://eu1.locationiq.com/v1/search.php?key=YOUR_PRIVATE_TOKEN&q=SEARCH_STRING&format=json
	public function get_coords_lociq(){//TODO: use city & postal code fields for request
		header("Access-Control-Allow-Origin: ". base_url() . "");

		error_log("get_coords_lociq");
		$key = "d73847e204479c";
		$url = "https://eu1.locationiq.com/v1/search.php";

		$param1 = "key";
		$param2 = "q";
		$param3 = "format=json";//med värde satt

		$this->load->helper('form');
		$this->load->library('form_validation');
		$form_data = $this->input->post();
		$this->form_validation->set_rules('street', 'Gata', 'trim|required');
		$this->form_validation->set_rules('postal_code', 'Postnummer', 'trim');
		$this->form_validation->set_rules('city', 'Stad', 'trim');
		$data['title'] = 'Se position för adress';

		$missing = "";

		if ($this->form_validation->run() === FALSE)
		{
			echo "Saknas gata";
		}
		else{
			error_log("form_data: " .  print_r($form_data, true));
			$street = trim($form_data["street"]);
			
			if(! is_numeric( substr($street, -1)) || is_numeric( substr($street, -2, 1))){
				$missing .= " Gatunummer\n ";
			}


			if(isset($form_data["city"]) && $form_data["city"] != ""){
				error_log("city isset");
				$city = " " . $form_data["city"];
			}
			else{ $city = ""; 
			error_log("city not set ");
			$missing .= " Stad\n ";
			}

			if(isset($form_data["postal_code"]) && $form_data["postal_code"] != ""){
				$postal_code = " " . $form_data["postal_code"];
			}
			else{
				$postal_code = "";
				$missing .= " Postnummer\n ";
			}

			$url_concat = $url."?$param1=$key&$param2=$street$city$postal_code&$param3";
			error_log($url_concat);// offer field for quarter (stadsdel) just for lociq
			$result = @file_get_contents($url_concat, "r");
			/*TODO: return all found coordinates*/
			/*
			 * result from locationiq is starting with smallest localisation
			 * like street, then bigger like quarter, then city, then county, then region
			 * then postal code, then country, then coordinates
			 * */
			if($result === FALSE){
				echo json_encode(array("message" => "Error"));
				exit();

			}
			if(empty($result)){
				error_log("Empty result");
				echo json_encode(array("message" => "No result"));
				exit();
			}
			/*
			 * Object
(
    [place_id] => 86686089
    [licence] => https://locationiq.com/attribution
    [osm_type] => way
    [osm_id] => 26937236
    [boundingbox] => Array
        (
            [0] => 63.8162454
            [1] => 63.817142
            [2] => 20.2605486
            [3] => 20.264153
        )

    [lat] => 63.8165113
    [lon] => 20.2614378
    [display_name] => Fiskegränd, Söderslätt, Teg, Umeå, Province Västerbotten, Västerbotten County, Region Norrland, 904 21, Sweden
    [class] => highway
    [type] => residential
    [importance] => 0.32
)
2019-08-02
			 */

			$res_arr = json_decode($result);
			$nr_answ = count($res_arr);
			error_log("Svar, antal: $nr_answ");
			if($nr_answ > 1){
			    $alternatives = array();//to send back
				error_log("Otydligt resultat");
			}

			//error_log("Resultat: " . print_r($res_arr, true) . " --------------- ");




			//tar första res
			$res1 = $res_arr[0];
			error_log(print_r($res1, true));
			$lat = $res1->lat;
			$lon = $res1->lon;
			//$this->wait_through_lock();
			usleep(500000);//to avoid over use

			/*
			$center_of_map_lat = $lat;//59.319178;
			$center_of_map_long =$lon;//18.095856;
			$zoom_level = 14;
			$url_map = "https://kartor.eniro.se/?embed=true&c=".$center_of_map_lat.",".$center_of_map_long."&z=".$zoom_level."&g=".$lat.",".$lon;

			$iframe = "$url_map<br><iframe width='100%' height='70%' src='$url_map' id='iframe1'></iframe>";
			 */ //out comm. 1 aug 19, - eniro replaced by leaflet maps

            $ret_arr = array("lat" => "$lat", "lon" => "$lon"/*, "karta" => "$iframe"*/);//return array

			if($nr_answ > 1){
				$ret_arr["message"] = "Otydligt resultat ($nr_answ olika), vänligen ange detaljer. ";
				if(strlen($missing)>0){
					$ret_arr["message"] .= "Detta saknas: $missing";
				}

				foreach($res_arr as $res){
				    error_log("res: " . print_r($res, true) . " --- ");
				    $alternatives[] = $res->display_name;
                }
				$ret_arr["alternatives"] = "<pre>" . implode("\n", $alternatives) . "</pre>";

			}
			else if($nr_answ == 1){
			    $ret_arr["message"] = "Hittade " . $res1->display_name;
            }
			$result = json_encode($ret_arr);


			echo $result;

		}

	}

	private function wait_through_lock(){
	    error_log("wait_through_lock");


        $filepath = "lockfile_for_source";
        touch($filepath);
        $fp = fopen("lockfile_for_resource", "r") or die("Could not open file.");

        while(true){
            while(!flock($fp, LOCK_EX)){
                error_log("Waiting");
                sleep(0.25); //wait to get file-lock.
            }

            $time = file_get_contents($filepath);
            $diff = time() - $time;
            if ($diff >= 1){
                break;
            }else{
                flock($fp, LOCK_UN);
            }
        }

        //Following code would never be executed simultaneously by two scripts.
        //You should access and use your resource here.

        fwrite($fp, time());
        fflush($fp);
        flock($fp, LOCK_UN); //remove lock on file.
        fclose($fp);
        //from kaspernj https://stackoverflow.com/questions/15121389/php-prevent-simultaneous-function-execution-throttling-limits-via-php
    }

	public function nr_is_above_0($num){
		return $num > 0;
	}

}
