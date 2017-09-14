<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

require APPPATH."third_party/MX/Controller.php";
require "../vendor/autoload.php";

use Carbon\Carbon;

class MY_Controller extends MX_Controller {
    
    public $admin_identifier = 'admin_management';

    public function __construct(){
        parent::__construct();

        // models
        $this->load->model('mgmt/M_session','',TRUE);
        $this->load->model('mgmt/M_registration','',TRUE);
        
        // default timezone
        date_default_timezone_set(app()->timezone);
    }
}

// untuk subclass dengan pengecekan login
class Admin_Controller extends MY_Controller {

    protected $session_exception = [];

    public function __construct(array $options = []){
        parent::__construct();

        // initialization
        $this->initialize($options);

        // cek sessionnya
        $this->session_check();
    }

    protected function initialize($options){
        foreach($options as $key => $row){
            if(isset($this->$key)){
                $this->$key = $row;
            }
        }
    }

    protected function session_check(){
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