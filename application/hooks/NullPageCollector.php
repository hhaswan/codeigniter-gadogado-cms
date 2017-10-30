<?php 
    
defined('BASEPATH') OR exit('No direct script access allowed');

class NullPageCollector
{
	private $CI;

	function __construct(){
		$this->CI =& get_instance();
	}

	// Bila server memberikan response dengan null (tetapi http_satatus = 200), maka function ini
	// akan melakukan intercept dengan men-set http_statusnya dengan 404 (Not Found)
	public function is_nulled(){
		if(ob_get_length()==0 and empty($this->CI->output->get_output())){
			$my = new Admin_controller;
			if(! empty(session($my->admin_identifier))){
				$this->CI->output->set_status_header(404);
			}
		}
	}
}