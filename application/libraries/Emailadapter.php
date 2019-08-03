<?php
defined('BASEPATH') OR exit('No direct script access allowed');

abstract class Emailadapter{

	abstract protected function send_mail($from, $to, $subject, $message);
}
