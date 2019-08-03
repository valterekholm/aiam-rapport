<?php
defined('BASEPATH') OR exit('No direct script access allowed');//?

if ( ! function_exists('send_mail'))
{
	/**
	 *
	 * @param	address - mottagare
     * @param   header - ämne
     * @param   message - meddelande
	 * @return	-
	 */
	function send_mail($address=FALSE, $header=FALSE, $message=FALSE)
	{
		$this->user_model->write_log("mail_helper.php send_mail (address, header, message) : ($address, $header, $message) ");
            if($address === FALSE && $header === FALSE && $message === FALSE){
                $this->load->helper('form');
                $this->load->library('form_validation');
                $form_data = $this->input->post();
                $address = $form_data["address"];//taget fr annan
                $header = $form_data["header"];
                $message = $form_data["message"];
            }

            $company = $this->company_model->get_first_company();
            $comp_name = $company["name"];
            $pos_ = strpos($comp_name, " ");
            if(FALSE===$pos_){
                $pos_ = strlen($comp_name);
            }

            $sender = $company["email"];

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
				$this->user_model->write_log("Gick inte");
                $this->load->view('pages/error');
            }
	    }
}

function send_mail_zoho($address=FALSE, $header=FALSE, $message=FALSE){

}
?>
