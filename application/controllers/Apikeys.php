<?php
class Apikeys extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->model('customer_model');
		$this->load->model('user_model');//för log i db
		$this->load->model('company_model');
		$this->load->model('apikeys_model');
		$this->load->helper('screen_out_helper');
		$this->load->helper(array('form', 'url'));
	}

	public function index()
	{
		$title = "Min nyckel för app";


		$key = $this->apikeys_model->get_key(base64_decode($_SESSION["user_id"]));
		error_log("contr index fick key $key");
		$data['key'] = $key;
		$data['title'] = $title;
		//$data["head_ext_css"] = "<link rel=\"stylesheet\" type=\"text/css\" href=\"".base_url()."/css/style.css\" />";
		header_view_footer('apikeys/index', $data);
	}

	public function make_my_key(){
		$title = "Generera slumpvis kod-nyckel";
		error_log("make_my_key");
		$string = $this->apikeys_model->random_str(60);

		$user_id = base64_decode($_SESSION["user_id"]);

		//delete old
		if(
			$this->apikeys_model->delete_my_keys($user_id)
		){
			error_log("deleted keys");
		}
		else{
			error_log("No key deleted");
		}

		$success = $this->apikeys_model->make_my_key($user_id);

		if($success){
			$data["message"] = "Sparade ny nyckel, tog bort gammal nyckel";
			header_view_footer('apikeys/success', $data);
		}
		else return false;
	}
}

?>
