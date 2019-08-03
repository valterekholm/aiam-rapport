<?php

include_once('Emailadapter.php');
class Emailadapter_1 extends Emailadapter{

	public function send_mail($from, $to, $subject, $message){
		//write to text-file instead
		$txt = "-------------------\nDate: " . date("Y-m-d") . "\n" . 
			"From: $from\n" .
			"To: $to\n" .
			"Subject: $subject\n" .
			"Message: $message\n";
		$countB = file_put_contents('email_messages.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);//TODO: write to special dir
			//$myfile = fopen("email_messages.txt", "a") or die("Unable to open file!");
			//fwrite($myfile, $txt);
			//fclose($myfile);
/*
 *
$this->load->helper('file');

$data = 'Some file data';
if ( ! write_file('./path/to/file.php', $data))
{
	echo 'Unable to write the file';
}
else
{
	echo 'File written!';
}
 *
 * */


		return $countB;
	}

}
