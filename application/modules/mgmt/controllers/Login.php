<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Admin_Controller {
        
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $data['title'] = "Login";
        $this->slice->view('login', $data);
    }
}
