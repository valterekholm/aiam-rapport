<?php
class Staff extends CI_controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('user_model');
		$this->load->model('company_model');
		$this->load->helper('url_helper');
		$this->load->helper('screen_out_helper');
		$this->load->helper('random_helper');
	}

	public function index()
	{
		$level = 0;
		$title = "All personal";
		if(empty($_SESSION["user_name"])){
			redirect('/');
		}
		else{
			$level = $this->user_model->get_level();
		}

		if($level == 1){
			$data['staff'] = $this->staff_model->get_staff();
			$company = null;
		}
		else if($level == 2){
			$data['staff'] = $this->staff_model->get_staff_in_my_company($_SESSION["user_name"]);
			$title .= " i mitt företag";
			$company = $this->company_model->get_company_data();
		}
		else{
			$data['staff'] = $this->staff_model->get_staff_by_id(base64_decode($_SESSION["user_id"]));
			$title = "Personal";
			$company = $this->company_model->get_company_data();
		}
		$data['title'] = $title;
		if($level>1){
			$data['title']	.= ", " . $company["name"];
		}
		$data['serv'] = $this->input->server('SERVER_NAME');//?
		//$data['head_ext_css'] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"../../../../admin/style.css\" />";
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		$data["head_ext_script"] = "<script type=\"text/javascript\" src=\"".
			                        base_url()."js/Script.js\" />";

		header_view_footer('staff/index', $data);
		/*$this->load->view('templates/header', $data);
		$this->load->view('staff/index', $data);
		$this->load->view('templates/footer');*/

	}

	public function view($email = NULL)
	{
		if(empty($_SESSION["user_name"])){
			redirect('/');

		}
		$data['person'] = $this->staff_model->get_staff($email);
		header_view_footer('staff/view', $data);
		/*
		$this->load->view('templates/header', $data);
		$this->load->view('staff/view');
		$this->load->view('templates/footer');*/

	}

	public function calendar(){
		$data['title'] = 'Kalender via Google';
		header_view_footer('staff/calendar', $data);

	}

	public function create()
	{
		if(empty($_SESSION["user_name"])){
			redirect('/');

		}
		$this->load->helper('form');
		$this->load->library('form_validation');

		$data['title'] = 'Spara info om personal';
		$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		$data["access_level"] = $this->user_model->get_level();

		$data["chain_table"] = get_table_chain("personal");

		$this->form_validation->set_rules('fname', 'Förnamn', 'trim|required');
		$this->form_validation->set_rules('ename', 'Efternamn', 'trim|required');//is_unique[users.email]
		$this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email');
		$this->form_validation->set_rules('tel', 'Telefon', 'trim|required');
		if($this->user_model->get_level() == 1){
			$this->form_validation->set_rules('company_id', 'Company-id', 'trim|required');
		}

		if ($this->form_validation->run() === FALSE)
		{
			header_view_footer('staff/create', $data);
		}
		else
		{
			$this->staff_model->set_staff();
			header_view_footer('staff/success');
		}
	}

	//exempel
	public function username_check($str)
	{
		if(empty($_SESSION["user_name"])){
			redirect('/');

		}
		if ($str == 'test')
		{
			$this->form_validation->set_message('username_check', 'The {field} field can not be the word "test"');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
		$this->user_model->write_log("Staff->username_check med $str");
	}//används i create genom att skriva 'callback_username_check' som sista arg i set_rules

	public function edit($id){
		if(empty($_SESSION["user_name"])){
			redirect('/');

		}
		$this->load->model("company_model");

		error_log("Staff edit med id $id");

		//passa på att rensa log
		$this->user_model->clear_old_log();

		//echo "Level: ".$this->user_model->get_level();
		if($this->user_model->get_level() == 3){
			$data["title"] = "Ej behörig";
			header_view_footer('/', $data);
		}

		$id = base64_decode($id);

		$this->load->helper('form');
		$this->load->library('form_validation');
		//$data['head_ext_script'] = "<script src=\"../../../../script/scripts.js\"></script>";
		$data['title'] = 'Redigera persons uppgifter';
		$person = $this->staff_model->get_staff_by_id($id);

		$data['person'] = $person;
		$data['level'] = $this->user_model->get_level();
		$data['company_info'] = $this->company_model->get_company_by_id($person["company_id"]);

		$data['companies'] = $this->company_model->get_companies_for_dropdown();
		error_log("data companies " . print_r($data['companies'], true));

		error_log("Company info: " . print_r($data['company_info'], true));
		header_view_footer('staff/edit', $data);

	}

	public function update(){
		$this->load->model('company_model');
		$level = $this->user_model->get_level();

		if(empty($_SESSION["user_name"])){
			redirect('/');
		}

		if($level > 2){
			redirect('/');
		}

		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['title'] = 'Update a staff record';

		$form_data = $this->input->post();
		if(isset($id)){//?
			$data['person'] = $this->staff_model->get_staff_by_id($id);
		}
		else{
			$id = $form_data["id"];
			$data['person'] = $this->staff_model->get_staff_by_id($id);
		}
		//print_r($data);
		$this->form_validation->set_rules('id','Id','required');
		$this->form_validation->set_rules('fname', 'Förnamn', 'trim|required');
		$this->form_validation->set_rules('ename', 'Efternamn', 'trim|required');//is_unique[personal.email]
		$this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email');
		$this->form_validation->set_rules('tel', 'Telefon', 'trim|required');
		$this->form_validation->set_rules('password', 'Lösenord', 'trim');
		$this->form_validation->set_rules('pcode', 'Kod', 'trim');

		$this->form_validation->set_rules('personnummer', 'Personnummer', 'trim');

		if($level == 1){
			$this->form_validation->set_rules('company', "Företag", 'required');
		}

		if ($this->form_validation->run() === FALSE)
		{
			$data['level'] = $level;
			$comp = $this->staff_model->get_staffmembers_company_id($id);
			$data['company_info'] = $this->company_model->get_company_by_id($comp);//set connected company
			header_view_footer('staff/edit', $data);
		}
		else
		{
			$this->staff_model->update_staff($level);
			header_view_footer('staff/success');
		}
	}

	public function null_company($id){
		if(empty($_SESSION["user_name"])){
			redirect('/');
		}

		if($this->user_model->get_level() > 1){
			redirect('/');
		}

		$data['title'] = 'Unconnect with staffmember';

		if(is_numeric($id)){
			if($this->staff_model->null_company($id)){
				$this->user_model->write_log("Staff->null_compoany lyckades");
				header_view_footer('staff/success');
			}
			else{
				echo "Gick inte";
				header_view_footer('pages/error');
			}
		}

	}

	public function delete_row($id){
		if(empty($_SESSION["user_name"])){
			redirect('/');

		}

		if($this->user_model->get_level() > 1){
			redirect('/');
		}
		$data['title'] = 'Delete a staff record';

		if(is_numeric($id)){
			if($this->staff_model->delete_staff_record($id)){
				$this->user_model->write_log("Staff->delete_row lyckades");
				header_view_footer('staff/success');
			}
			else{
				echo "Gick inte";
			}
		}
		$this->user_model->write_log("Staff->delete_row ($id)");
	}

	public function set_password_post(){

		$this->user_model->write_log("Set_password_post contr.");
		$this->load->helper('form');
		$this->load->library('form_validation');


		//test-bara
		$p = $_POST;
		//print_r($p);

		$data['title'] = 'Sätt nytt lösenord';
		$this->form_validation->set_rules('id', 'person', 'trim|required');
		$this->form_validation->set_rules('pcode', 'kod', 'trim');
		$this->form_validation->set_rules('password', 'lösenord', 'trim|required|min_length[4]|max_length[17]');

		if ($this->form_validation->run() === FALSE)
		{
			$this->user_model->write_log("Set_password_post, formulär ej godkänt");
			$data['title'] = 'Kunde inte sätta password';
			header_view_footer('staff/set_password', $data);
		}
		else{
			$this->user_model->write_log("Set_password_post, formulär godkänt!");
			if($this->staff_model->set_password()){
				$id = $this->input->post("id");
				$this->user_model->write_log("Kunde köra set password okej, ska använda id ($id)");
				if($this->staff_model->set_random_code()){
					$this->user_model->write_log("Kunde ändra kod");
					echo "<p>Bytte ut kod</p>";
					header_view_footer('staff/success');
				}
				else{
					$this->user_model->write_log("Kunde inte sätta kod");
				}
			}
			else{
				$this->user_model->write_log("Kunde inte sätta password");
				exit("Kunde inte sätta password");
				//show_error("Kunde inte sätta lösenord, " . $this->db->_error_message(), "");//12 sep -17
				$data['title'] = 'Kunde inte sätta password';
				header_view_footer('staff/index', $data);
			}
		}
		$this->user_model->write_log("Staff->set_password_post");
	}

	public function set_password($person_=FALSE,$code_=FALSE){

		error_log("set_password med $person_ och $code_");
		$this->load->helper('form');
		$this->load->library('form_validation');


		$data['head_ext_css'] = "<link rel='stylesheet' href=\"".base_url()."/css/style.css\">";

		$data['title'] = 'Sätt nytt lösenord';
		$this->form_validation->set_rules('id', 'person', 'trim|required');
		$this->form_validation->set_rules('pcode', 'kod', 'trim|required');
		$this->form_validation->set_rules('password', 'lösenord', 'trim|required');


		if(isset($person_) && isset($code_)){
			$this->user_model->write_log("Fick in parametrar person och code");
			$person = base64_decode($person_);
			$data["code"] = $code = base64_decode($code_);
			$data["person"] = $this->staff_model->get_staff_by_id_and_code($person, $code);

			if(empty($data["person"])){
				echo "<p>Ingen person hittad, kontakta avsändaren...</p>";
			}
			else{
				//person är vald
				//
				$this->user_model->write_log("data[person] är fyllt med tex id " . $data["person"]["id"]);

			}

		}



		header_view_footer('staff/set_password', $data);
	}

	public function send_mail_2($address=FALSE, $header=FALSE, $message=FALSE){
		$this->user_model->write_log("Inne i Staff->send_mail_2");
		if($address === FALSE && $header === FALSE && $message === FALSE){
			$this->load->helper('form');
			$this->load->library('form_validation');
			$form_data = $this->input->post();
			$address = $form_data["address"];//taget fr annan
			$header = $form_data["header"];
			$message = $form_data["message"];
		}

		$this->staff_model->send_mail($address, $header, $message);
	}

	public function send_mail($address=FALSE, $header=FALSE, $message=FALSE){
		$this->load->helper('form');

		error_log("send_mail");
		if($address === FALSE && $header === FALSE && $message === FALSE){
			$address = $this->input->post("address");
			$header = $this->input->post("header");
			$message = $this->input->post("message");
		}
		$this->user_model->write_log("Inne i Staff->send_mail... address = $address , header = $header , message = $message");
		$bytes_written = $this->staff_model->send_mail_3("noreply@Jobb_rapport_systemet.se", $address, $header, $message);//test

		//error_log("bytes written: $bytes_written");

		if($bytes_written > 0){
			header_view_footer('staff/success');
		}
		else{
			header_view_footer('pages/error');
		}
		return 0;

		if($address === FALSE && $header === FALSE && $message === FALSE){
			$this->load->helper('form');
			$this->load->library('form_validation');
			$form_data = $this->input->post();
			$address = $form_data["address"];//taget fr annan
			$header = $form_data["header"];
			$message = $form_data["message"];
		}

		$company = $this->staff_model->get_agents_company();
		$comp_name = $company["name"];
		$pos_ = strpos($comp_name, " ");
		if(FALSE===$pos_){
			$pos_ = strlen($comp_name);
		}

		$sender = $this->email_;//test

		$this->load->library('email');

		$this->email->from($sender, $comp_name);
		$this->email->to($address);
		//$this->email->cc('another@another-example.com');
		//$this->email->bcc('them@their-example.com');

		$this->email->subject($header);
		$this->email->message($message);

		$this->user_model->write_log("Ska skicka email (from, to, subject, message) : (($sender, $comp_name), $address, $header, $message)");

		if($this->email->send()){
			$this->user_model->write_log("lyckades");
			header_view_footer('staff/success');
		}
		else{
			$this->user_model->write_log("det gick inte");
			header_view_footer('pages/error');
		}
	}

	public function send_mail_3($to=FALSE, $subject=FALSE, $message=FALSE){
		error_log("send_mail_3 i Controller");
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('emailadapter_1');

		$this->form_validation->set_rules('to', 'Mottagare', 'trim|required');
		$this->form_validation->set_rules('subject', 'Ämnesrad', 'trim|required');
		$this->form_validation->set_rules('message', 'Meddelande', 'trim|required');
		error_log(print_r($this->input->post), true);
		if ($this->form_validation->run() === FALSE){
			error_log("form_validation ej godk");
		}
		else{
			if(!$to && !$subject && !$message){
				error_log("Parametrar är tomma");
				$to = $this->input->post("to");
				$subject = $this->input->post("subject");
				$message = $this->input->post("message");
			}
			$from = "noreply@Jobb_rapport_systemet.se";
			error_log("to $to $subject $message ...");
			$countB = $this->emailadapter_1->send_mail($from, $to, $subject, $message);

			if($countB > 0){
				error_log("Wrote $countB");
				header_view_footer('staff/success');
			}
			else{
				error_log("Nothing written");
				header_view_footer('pages/error');
			}
		}
	}

	//Used to send emails to users to ask them set password
	public function send_mail_company(){
		$this->user_model->write_log("Staff->send_mail_company");
		$this->load->helper('form');
		$this->load->library('form_validation');
		$form_data = $this->input->post();

		$server_name = $_SERVER['SERVER_NAME'];
		if($server_name == "localhost"){
			echo "Skickar ej fr localhost";
		}
		else if($server_name == "www.laialflytt.se" || $server_name == "laialflytt.se"){



		/*
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=iso-8859-1';
		$headers[] = 'To: <'.$email.'>';
		$headers[] = 'From: '.$this->company_name.' systemet <info@laial.se>';
		 */

			$to = $this->email_ . "; valterekholm@hotmail.com";
			// the message
			$msg = "Meddelande från datasystemet...\n\n" . $form_data["message"];

			$msg = str_replace("\n.", "\n..", $msg);
			$subject=$form_data["header"];
			// use wordwrap() if lines are longer than 70 characters
			$msg = wordwrap($msg,70);
			$headers='From:'.$this->email_."\r\n".'Reply-To: '.$this->email_."\r\n".'X-Mailer: PHP/'.phpversion();//info@laialflytt.se
			//om ej fungerar
			$id = $form_data["id"];
			//implode("\r\n", $headers)
			// send email
			if(mail($to,$subject,$msg,$headers)){
				echo "<p>Skickade meddelande " . $msg . "</p>";
				$data["title"] = "Skickat";
				header_view_footer('staff/success');
			}
			else{
				$data["title"] = "Kunde ej skicka";
				header_view_footer('staff/edit');
			}
		}
		else{
			echo "Annat server name; $server_name";
		}
	}

}
