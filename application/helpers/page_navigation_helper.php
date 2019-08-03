<?php
defined('BASEPATH') OR exit('No direct script access allowed');//?

if ( ! function_exists('is_start_page'))
{

	/*
	 *
	 * param base_url - corresponds to CI base_url()
	 *
	 * */
function is_start_page(base_url){
	if( substr(base_url, strrpos(rtrim(base_url, '/'), '/')) == $_SERVER['REQUEST_URI'] ){
		return true;
	}
	return false;
}

}
