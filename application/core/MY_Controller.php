<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

require APPPATH."third_party/MX/Controller.php";
require "../vendor/autoload.php";

use Carbon\Carbon;

class MY_Controller extends MX_Controller {
    
    protected $admin_identifier = 'admin_management';

    public function __construct(){
        parent::__construct();

        // models
        $this->load->model('mgmt/M_session','',TRUE);
        
        // default timezone
        date_default_timezone_set(app()->timezone);
    }
}

// untuk subclass dengan pengecekan login
class Admin_Controller extends MY_Controller {
    public function __construct(){
        parent::__construct();

        // cek sessionnya
        $this->session_check();
    }

    protected function session_check(){
        if(! session($this->admin_identifier)){
            // redirect ke login page
            redirect(base_url(), 'refresh');
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