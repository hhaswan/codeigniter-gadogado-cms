<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

require APPPATH."third_party/MX/Controller.php";
require "../vendor/autoload.php";

use Carbon\Carbon;

class MY_Controller extends MX_Controller {
    public function __construct(){
        parent::__construct();
        
        // default timezone
        date_default_timezone_set(app()->timezone);
    }
}

// untuk subclass dengan pengecekan login
class Admin_Controller extends MY_Controller {
    public function __construct(){
        parent::__construct();
    }
}

// untuk subclass tanpa pengecekan login
class Front_Controller extends MY_Controller {

    public function __construct(){
        parent::__construct();
    }
}