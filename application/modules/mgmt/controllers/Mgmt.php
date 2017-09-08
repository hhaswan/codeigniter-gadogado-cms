<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

class Mgmt extends Admin_Controller {
        
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        // print_r(Carbon::now()->setTimeZone('Asia/Makassar'));

        $data['title'] = "Beranda";
        $this->slice->view('index', $data);
    }
}
