<?php
class Call_api extends MY_Controller {

    public function __construct()
    {
    parent::__construct();
    $this->load->library('session');
    $this->load->model('customer_model');
    $this->load->model('user_model');//för log i db
    $this->load->helper('url_helper');
    $this->load->helper('form_helper');
    $this->load->helper('screen_out_helper');
    }

    public function send(){
        include "./jobb_rapport/httpful.phar";

        //exempel
        $response = \Httpful\Request::get('http://example.com')->send();
    }
}
