<?php
class Staff_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');

		/*$this->load->model("staff_model");*/
		//$this->load->model("company_model");

		/*$company = $this->company_model->get_first_company();
		$this->company_name = $company["name"];
		$this->street = $company["gatuadress"];
		$this->postal_code = $company["postnummer"];
		$this->email_ = $company["email"];
		$this->phone = $company["telefon"];
		$this->cctld = $company["cctld"];*/
	}

	public function get_staff($email=FALSE){
		if($email === FALSE){ //if no email set, gets all
			$query = $this->db->get('personal');
			return $query->result_array();
		}

		$query = $this->db->get_where('personal', array('email' => $email));
		return $query->row_array();
	}

	public function get_staff_in_my_company($user_name=FALSE){//email
		if($user_name === FALSE){
			error_log("user_name false");
			return null;
		}
		$query = $this->db->get_where('personal', array('email' => $user_name));
		$staff = $query->row();
		$company_id = $staff->company_id;

		$query2 = $this->db->get_where('personal', array('company_id' => $company_id));

		return $query2->result_array();

	}

	public function get_staff_by_id($id=FALSE){
		if($id === FALSE){
			$query = $this->db->get('personal');
			return $query->result_array();
		}

		$query = $this->db->get_where('personal', array('id' => $id));
		return $query->row_array();
	}

	public function get_staff_by_email($email=FALSE){
		if($email === FALSE){
		}

		$query = $this->db->get_where('personal', array('email' => $email));
		return $query->row_array();
	}

	public function get_staff_by_id_and_code($id=FALSE, $code=FALSE){
		error_log("get_staff_by_id_and_code med $id och $code");
		if($id === FALSE|| $code === FALSE ){
			error_log("argm fattas");
			return FALSE;
		}

		$query = $this->db->get_where('personal', array('id' => $id, 'code' => $code));
		if($query->num_rows()>0){
			error_log("Hej " . implode(", ", $query->row_array()));
		}
		else{
			error_log("Inga rader funna");
			//return FALSE;
		}
		return $query->row_array();
	}

	/*returns only id*/
	public function staff_by_code($code=FALSE){
		if($code === FALSE){
			return false;
		}

		$query = $this->db->get_where('personal', array('code' => $code));
		if($query->num_rows()>0){
			foreach($query->result() as $row){
				return $row->id;
			}
		}
		return false;
	}

	//argument id - person-id
	public function get_jobs_for_person($id=FALSE){
		if($id === FALSE){
			exit();
		}

		$query = $this->db->query("select at.id, datum_start, datum_slut, beskrivning, fornamn from arbets_tillfalle at left join personal_arbetstillfalle on(id=id_arbetstillfalle) left join personal on(id_person = personal.id) where personal.id=$id");
		return $query->result_array();
	}

	public function list_staff(){
		$query = $this->db->query('SELECT fornamn,efternamn,email,tel FROM personal');

		foreach ($query->result() as $row)
		{
			echo $row->title;
			echo $row->name;
			echo $row->email;
		}
		echo 'Total Results: ' . $query->num_rows();
	}

	public function view($staff_email)
	{
		$data['person'] = $this->staff_model->get_staff();
		if (empty($data['person']))
		{
			show_404();
		}
		$data['title'] = $data['person']['fornamn'];
		$this->load->view('templates/header', $data);
		$this->load->view('staff/view', $data);
		$this->load->view('templates/footer');
	}

	public function edit($email){
		$data['person'] = $this->staff_model->get_staff();
		$this->user_model->write_log("Staff_model->edit, " . print_r($data, true));
		if (empty($data['person']))
		{
			show_404();
		}
		$data['title'] = $data['person']['fornamn'];
		$this->load->view('templates/header', $data);
		$this->load->view('staff/edit', $data);
		$this->load->view('templates/footer');
	}

	public function set_staff()
	{
		//password_hash("rasmuslerdorf", PASSWORD_DEFAULT)
		$this->load->helper('url');
		$data = array(
			'fornamn' => $this->input->post('fname'),
			'efternamn' => $this->input->post('ename'),
			'email' => $this->input->post('email'),
			'tel' => $this->input->post('tel'),
			'personnummer' => '0000000000',
			'code' => $this->get_random_code(10)
		);
		$level = $this->user_model->get_level();
		if($level == 1){
			$data['company_id'] = $this->input->post('company_id');
		}
		else if($level > 1){
			$data['company_id'] = $this->get_agents_company_id();

			if($level == 3 && $data['email'] != $_SESSION['user_name']){
				return null;//om level3 försöker spara någon annans uppgifter
			}
		}
		else return false;

		error_log("Ska spara person: " . print_r($data, true));

		return $this->db->insert('personal', $data);
	}

	public function update_staff($level)
	{
		$touch_password = false;
		$this->load->helper('url');
		$data = array(
			'id' => $this->input->post('id'),
			'fornamn' => $this->input->post('fname'),
			'efternamn' => $this->input->post('ename'),
			'email' => $this->input->post('email'),
			'tel' => $this->input->post('tel'),
			'personnummer' => $this->input->post('personnummer'),
			'level' => $this->input->post('level')
		);
		if($level == 1){
			$data['company_id'] = $this->input->post('company'); 
		}
		else{//if not change company
			$data['company_id'] = $this->staff_model->get_staffmembers_company_id($data["id"]);
		}

		if(strlen($this->input->post('password'))>0){
			$data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
			$touch_password = true;
		}
		else{
			//leave password
		}
		if(strlen($this->input->post('pcode'))>0){
			$data['code'] = $this->input->post('pcode');
		}
		else{
			$data['code'] = $this->get_random_code(10);//sätt en slumpvis
		}

		if($touch_password){
			return $this->db->replace('personal',$data);
		}
		else{
			$this->db->where('id', $data["id"]);
			return $this->db->update('personal', $data);
		}
	}

	public function null_company($id){
		$data = array('company_id' => null);
		$this->db->where('id', $id);
		$this->db->update('personal',$data);

		return $this->db->affected_rows() == 1;
	}


	public function delete_staff_record($id){
		$this->db->where('id',$id);
		return $this->db->delete('personal');
	}

	public function get_random_code($len){//för email utskick för nytt lösenord m.m.
		$this->load->model('user_model');
		$stack = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@&";
		error_log("stack: $stack");
		$min = 0;
		$max = strlen($stack)-1;

		$code ="";

		for($id=0; $id<$len; $id++){
			if(function_exists("random_int")){
				$ind = random_int($min, $max);
				$new = $stack[$ind];
				error_log("$id: $ind $new");
				$code .= $new;
			}
			else{
				$code .= $stack[rand($min, $max)];
			}
		}
		error_log("Har genererat $code");
		return $code;
	}

	public function set_random_code($id=FALSE){
		$this->load->helper('url');
		$this->load->model('user_model');
		if($id===FALSE){
			$id = $this->input->post('id');
		}
		$new_code = $this->get_random_code(10);
		$this->user_model->write_log("set_random_code $new_code");
		$data = array(
			'code' => $this->db->escape($new_code)
		);

		$this->db->where('id',$id);
		return $this->db->update('personal', $data);
	}

	public function get_staffmembers_company($email){
		error_log("get_staffmembers_company");

		$staff = $this->get_staff_by_email($email);
		//error_log(print_r($staff), true);
		if($staff["level"]>1){
		$query = $this->db->get_where('company_info', array('id' => $staff["company_id"]));
		return $query->row_array();
		}
		else{
			return null;
		}
	}

	public function get_staffmembers_company_id($user_id){
		/*error_log("get_staffmembers_company_id($user_id)");*/
		$staff = $this->get_staff_by_id($user_id);
		if($staff["level"]>1){
			return $staff["company_id"];
		}
		else return null;
	}

	public function get_staff_by_company($comp_id){

		$query = $this->db->get_where('personal', array('company_id'=>$comp_id));
		return $query->result_array();
	}

	public function get_companys_staff_ids($comp_id){
		$query = $this->db->query("SELECT id FROM personal WHERE company_id = $comp_id");
		$res = $query->result_array();
		$r = array();
		foreach($res as $row){
			$r[] = $row["id"];
		}
		return $r;
	}

	//the current users company id
	//TODO: check if admin 1 should be forced to have a company
	// ... else this admin 1 maybe cant use this function
	public function get_agents_company_id(){
		/*error_log("get_agents_company_id");*/
		if($this->user_model->get_level() < 2){
			return null;
		}
		/*error_log("Ska hämta personals företags id utifrån " .
		 * $this->session->user_id . " avkodat: " .
		 * base64_decode($this->session->user_id));*/
		return $this->get_staffmembers_company_id(base64_decode($this->session->user_id));
	}

	public function get_agents_company(){
		$this->load->model("company_model");
		if($this->user_model->get_level() < 2){
			return null;
		}

		return $this->company_model->get_company_by_id($this->get_agents_company_id());
	}

	public function set_password(){
		$this->load->helper('url');
		$id = $this->input->post('id');
		//$query = $this->db->get_where('personal', array('id' => $id));

		$this->user_model->write_log("I set password ska använda " . $this->input->post('password') . " för id $id");

		$data = array(
			//'code' => $this->input->post('code'), // kanske om man skulle göra nytt pw direkt
			'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
		);
		//var_dump($data);
		$array = array('id' => $id);
		$this->db->where($array);
		return $this->db->update('personal', $data);
		//sen gör ny code
	}

	//Sends mail 
	public function send_mail($address=FALSE, $header=FALSE, $message=FALSE){
		$this->user_model->write_log("Staff_model->send_mail med (address, header, message):($address, $header, $message)");
		$this->email->initialize($config);//test
		if($address === FALSE && $header === FALSE && $message === FALSE){
			$this->user_model->write_log("All params empty");
			$this->load->helper('form');
			$this->load->library('form_validation');
			$form_data = $this->input->post();
			$address = $form_data["address"];//taget fr annan
			$header = $form_data["header"];
			$message = $form_data["message"];
		}
		else{
			$this->user_model->write_log("Parameters were set");
		}

		if($this->user_model->get_level() == 1)
			$company = $this->company_model->get_first_company();
		else
			$company = $this->staff_model->get_agents_company();

		$comp_name = $company["name"];
		$pos_ = strpos($comp_name, " ");
		if(FALSE===$pos_){
			$pos_ = strlen($comp_name);
		}

		$sender = $this->email_;//detta från job controller

		$this->load->library('email');

		$this->email->from($sender, $comp_name);
		$this->email->to($address);
		//$this->email->cc('another@another-example.com');
		//$this->email->bcc('them@their-example.com');

		$this->email->subject($header);
		$this->email->message($message);

		if($this->email->send()){
			$this->user_model->write_log("Lyckades");
			$this->load->view('staff/success');
		}
		else{
			$this->user_model->write_log("Det gick inte");
			$this->load->view('pages/error');
		}
	}
	public function send_mail_2($address=FALSE, $header=FALSE, $message=FALSE){
		$this->load->library('email');

		$this->email->from('your@example.com', 'Your Name');
		$this->email->to('someone@example.com');
		$this->email->cc('another@another-example.com');
		$this->email->bcc('them@their-example.com');

		$this->email->subject('Email Test');
		$this->email->message('Testing the email class.');

		$this->email->send();
	}

	public function send_mail_3($from, $to, $subject, $message){

		//error_log("send_mail_3 i staff model from $from $to $subject $message");
		$this->load->library('emailadapter_1');
		$countB = $this->emailadapter_1->send_mail($from, $to, $subject, $message);
		//TODO: use emailadapter_2 when ready
		//error_log("to $to $subject $message ...");
		return $countB;
	}
}
