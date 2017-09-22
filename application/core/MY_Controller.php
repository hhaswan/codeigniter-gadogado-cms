<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

require APPPATH."third_party/MX/Controller.php";
require "../vendor/autoload.php";

use Carbon\Carbon;

class MY_Controller extends MX_Controller {
    
    public $admin_identifier        = 'admin_management';
    public $request_method_post     = false;
    public $request_method_get      = false;
    public $request_method_put      = false;
    public $request_method_patch    = false;
    public $request_method_delete   = false;
    public $request_data            = [];

    public function __construct(){
        parent::__construct();

        // models
        $this->load->model('mgmt/M_role','',TRUE);
        $this->load->model('mgmt/M_session','',TRUE);
        $this->load->model('mgmt/M_registration','',TRUE);
        
        // default timezone
        date_default_timezone_set(app()->timezone);

        $this->_iniziatizate();
    }

    function _iniziatizate(){
        // request method user
        switch($this->input->method()){
            case "post":
                $this->request_method_post      = true;
                break;
            case "put":
                $this->request_method_put       = true;
                break;
            case "patch":
                $this->request_method_patch     = true;
                break;
            case "delete":
                $this->request_method_delete    = true;
                break;
            default:
                $this->request_method_get       = true;
                break;
        }

        // dapatkan request data user
        $this->request_data = $this->input->input_stream();
    }
}

// untuk subclass dengan pengecekan login
class Admin_Controller extends MY_Controller {

    protected $session_exception    = [];
    protected $user_data            = [];
    protected $user_permission      = [];

    public function __construct(array $options = []){
        parent::__construct();

        $this->initialize($options);
        $this->session_check();
        $this->get_data_user();
    }

    protected function initialize($options){
        // initialization
        foreach($options as $key => $row){
            if(isset($this->$key)){
                $this->$key = $row;
            }
        }
    }

    protected function get_data_user(){
        $output = [];
        
        // get nama role
        if($this->session_check()){
            $output += session($this->admin_identifier);
            if($q = $this->M_role->get(null, [ 'id' => $output['role_id'] ])){
                $output += [ 'role_name' => $q[0]->name, 'alias' => $q[0]->alias ];
            }
        }

        return $this->user_data = (object) $output;
    }

    protected function permission_check(){

        // cek permission user untuk mengakses halaman tertentu

    }

    protected function session_check(){
        // cek sssion user
        if(! session($this->admin_identifier)){

            // show halaman 404
            if(! in_array(access()->method, $this->session_exception)){
                show_404();
            }
        }
    }
}

// untuk subclass tanpa pengecekan login
class Front_Controller extends MY_Controller {

    public function __construct(){
        parent::__construct();

        // bila landing page tidak diaktifkan maka tampilkan halaman error
        if(! app()->landing_page){
            show_404();
        }
    }
}