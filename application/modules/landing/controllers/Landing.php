<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends Front_Controller {
        
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $this->slice->view('index');
    }
	
	public function read(){
        $this->slice->view('index');
    }
}
