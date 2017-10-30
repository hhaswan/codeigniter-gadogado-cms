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
        $this->load->model('mgmt/M_user','',TRUE);
        $this->load->model('mgmt/M_module','',TRUE);
        $this->load->model('mgmt/M_session','',TRUE);
        $this->load->model('mgmt/M_permission','',TRUE);
        $this->load->model('mgmt/M_division','',TRUE);
        $this->load->model('mgmt/M_setting','',TRUE);
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
    protected $user_permission      = false;
    protected $user_priviledge      = null;
    protected $is_exception         = false;


    public function __construct(array $options = []){
        parent::__construct();

        $this->initialize($options);
        $this->session_check();
        $this->get_data_user();
        $this->permission_check();

        // bila statusnya development, ijinkan semua access utk admin
        if(! $this->is_exception){
            if(ENVIRONMENT != 'development'){
                if(! $this->user_permission){
                    $this->_show_error(403);
                }
            }
        }
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

                // dapatkan divisi dari user ini
                if($divisi = $this->M_division->get_user_division(['app_users_id' => $output['user_id']])){
                    // dapatkan nama divisi
                    $n_d = $this->M_division->get(null,['id' => $divisi[0]->app_divisions_id]);
                    if($n_d){
                        // dapatkan area yang dapat diakses oleh user ini
                        $area = [];
                        if($n_d[0]->is_access_all != 1){
                            foreach ($this->M_division->get_access_division(['app_divisions_id' => $divisi[0]->app_divisions_id]) as $value) {
                                array_push($area, $value->division_access);
                            }
                        }

                        $output += [ 
                            'division_name' => $n_d[0]->name, 
                            'division_all'  => $n_d[0]->is_access_all, 
                            'division_id'   => $n_d[0]->id, 
                            'division_area' => $area 
                        ];
                    }
                }

                // add your app's custom session object                
            }
        }

        return $this->user_data = (object) $output;
    }

    protected function permission_check(){

        // bila masuk dalam masa development, 
        // ijinkan semua priviledgenya untuk semua user
        if(ENVIRONMENT == 'development'){
            $preset_priv = true;
        }else{
            $preset_priv = false;
        }

        $output = [
            'edit'   => $preset_priv,
            'detail' => $preset_priv,
            'delete' => $preset_priv,
            'add'    => $preset_priv
        ];

        // masukkan method yang berlaku secara global dan ingin diexclude dari
        // pengecekan permission
        $exclude = [ 'ajax_', 'mgmt/mgmt/index' ];

        // dapatkan akses untuk module, class dan access 
        // yang sedang diakses oleh user ini
        $acs = $this->M_module->get('app_access', [ 
            'app_modules_id'    => access()->module, 
            'class_name'        => access()->controller,
            'access_name'       => access()->method
        ]);

        // bila ada entri untuk akses ini
        if($acs){
            
            // cek permission user untuk mengakses halaman tertentu
            $priv = $this->M_permission->get(null, [
                'app_access_id' => $acs[0]->id, 
                'app_roles_id'  => $this->user_data->role_id
            ]);

            if($priv){
                // permission per page
                $this->user_permission = true;
    
                // priviledge per page
                if($priv[0]->edit == 1){
                    $output['edit']     = true;
                }if($priv[0]->detail == 1){
                    $output['detail']   = true;
                }if($priv[0]->delete == 1){
                    $output['delete']   = true;
                }if($priv[0]->add == 1){
                    $output['add']      = true;
                }
            }
        }

        // periksa method-method yang termasuk dalam exclude
        foreach($exclude as $row){
            $mod    = null;
            $ctrl   = null;
            $meth   = $row;
            
            // apabila entri dalam exclude itu dalam format a/b/c maka 
            // anggap dengan format: module/controller/method

            $x = explode('/', $row);
            if(count($x) > 1){
                if(isset($x[0])){
                    $mod    = $x[0];
                }if(isset($x[1])){
                    $ctrl   = $x[1];
                }if(isset($x[2])){
                    $meth   = $x[2];
                }

                // bila module/controller/access match maka ijinkan aksesnya
                if(access()->module == $mod){
                    if(access()->controller == $ctrl){
                        if(access()->method == $meth){
                            // ijinkan permission
                            $this->user_permission = true;
                        }
                    }
                }
            }else{

                // hanya untuk scope method saja, jadi semua method yang punya nama
                // sama dengan ini akan diijinkan aksesnya. 
                // GUNAKAN DENGAN BIJAKSANA
                if(strpos(access()->method, $meth) !== false){
                    // ijinkan permission
                    $this->user_permission = true;
                }
            }
        }

        // module exception, langkahi pemeriksaan permissionnya
        if(in_array(access()->method, $this->session_exception)){
            $this->user_permission = true;
        }

        $this->user_priviledge = (object) $output;
    }

    protected function session_check(){
        // cek sssion user
        if(! session($this->admin_identifier)){

            // show halaman 404
            if(! in_array(access()->method, $this->session_exception)){
                // bila tidak secure login maka dianggap halaman loginnya bisa diakses oleh siapa saja
                if(app()->secure_login){
                    show_404(); 
                }else{
                    flash(['MSG_ERROR' => "Anda harus login terlebih dahulu."]);
                    redirect(base_url('/login'));
                }
            }
        }else{
            return true;
        }
    }

    function _set_notification($userid, $title, $content = null, $url = null){
        $output = false;
        
        $data = [
            'id'            => random_string('alnum', 100),
            'title'         => $title,
            'content'       => $content,
            'app_users_id'  => $userid,
            'url'           => (! isset($url)) ? '#' : $url,
            'created_at'    => Carbon::now(),
            'is_read'       => 0
        ];
        
        // jangan kirim ke diri sendiri
        if($userid != $this->user_data->user_id){
            if($this->M_user->insert('app_notifications', $data)){
                $output = true;
            }   
        }

        return $output;
    }

    function _show_error($code = 404, $message = null){
        // hapus semua tampilan sebelumnya
        // ini karena sebelum method ini dipanggil CI sudah merender view permethodnya
        // sehingga akan muncul halaman method dan halaman error dalam satu layar
        ob_get_clean();
        
        // datar http code dan penjelasan
        $list_error = [
            404 => 'Halaman Tidak Ditemukan',
            500 => 'Internal Server Error',
            403 => 'Akses Ditolak'
        ];

        // set http codenya
        $this->output->set_status_header($code);

        // title
        $title = (isset($list_error[$code])) ? $list_error[$code] : null;

        $data['error_code'] = $code;
        $data['error_page'] = 1;
        $data['error_msg']  = $message;
        $data['title']      = $title;
        die($this->slice->view('commons.error', $data, true));
    }

    function _remap($method, $params = array()){
        
        // bila method exsists
        if(method_exists($this, $method)){
            return call_user_func_array(array($this, $method), $params);
        }else{
            // tampilkan error 404
            $this->_show_error();
        }
    }
}

// untuk subclass tanpa pengecekan login
class Front_Controller extends MY_Controller {

    public function __construct(){
        parent::__construct();

        $this->initialize();
    }

    protected function initialize(){
        // bila landing page tidak diaktifkan maka tampilkan halaman error
        if(! app()->landing_page && access()->controller != 'login'){
            // bila login tidak memerlukan akses secure, maka redirect 
            // ke halaman login biasa saja, bila secure tampilkan halaman 404
            if(! app()->secure_login){
                redirect(base_url('/login'), 'refresh');
            }else{
                show_404();
            }
        }
    }
}