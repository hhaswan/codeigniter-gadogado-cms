<?php 
    
defined('BASEPATH') OR exit('No direct script access allowed');

class PageSecurity
{
	private $CI;

	function __construct(){
		$this->CI =& get_instance();
	}

	// Fitur ini secara otomatis menambahkan request token ke dalam form html (POST)
	public function rerender_output(){

		$csrf_enabled 	= $this->CI->config->item('csrf_protection');

		// Render hanya pada saat page berhasil ditampilkan (200) dan tipe kontentnya adalah html
		if(http_response_code()==200 && strpos($this->CI->output->get_content_type(), "html")!==false && $csrf_enabled){
			$page_output 	= null;

			// Tidak pake library output CI untuk mendapatkan hasil dari echo tanpa melalui library output CI
			// Cek di dua output berbeda ob (output buffer itu bersifat echo langsung dari controller, get_output merupakan view)
			$cont = ob_get_contents();
			$cont .= $this->CI->output->get_output();
			
			// Hapus output buffer sebelumnya agar tampilan tidak ter append
			ob_clean();
			if($csrf_enabled){
				$page_output = preg_replace('/(<form method="post"[^>]+>)/i', "$1\n<input type=\"hidden\" name=\"{$this->CI->security->get_csrf_token_name()}\" value=\"{$this->CI->security->get_csrf_hash()}\"/>", $cont);
			}
			
			$this->CI->output->set_output($page_output);
		}
	}
}