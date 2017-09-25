<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

class Module extends Admin_Controller {

    protected $module = "Module"; 
        
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $data['priv']   = $this->user_priviledge;
        $data['body']   = $this->_result_table();
        $data['title']  = "Management {$this->module}";
        $this->slice->view('module.index', $data);
    }

    public function create(){
        if(! post('submit')){
            // bukan post, maka tampilkan halaman create

            // list module yang belum terinstall
            $mod    = [];
            $path   = APPPATH.'modules';

            foreach (scandir($path) as $row) {
                if ($row === '.' || $row === '..') continue;
                if (is_dir($path.DS.$row)) {
                    // cek modul yang belum ada di database
                    if(! $this->M_module->get(null, [ 'id' => strtolower($row) ])){
                        // dapatkan meta modul
                        if(is_file($meta = $path.DS.$row.DS.'install.txt')){
                            // baca meta modul
                            $cont   = file_get_contents($meta);
                            $ex     = str_replace("|", " - ", $cont);

                            // masukkan dalam list
                            $mod    += [ $row => $ex ];
                        }
                    }
                }
            }

            $data['modules']    = form_dropdown('module', $mod, null, 'class="form-control selectpicker" data-live-search="true"');
            $data['priv']       = $this->user_priviledge;            
            $data['body']       = $this->_result_table();
            $data['title']      = "Tambah {$this->module}";
            $this->slice->view('module.create', $data);
        }else{
            // validate
            $form_validate = validation([
                ['module', 'Daftar Module', 'xss_clean'],
                ['new_module', 'Module Baru', 'xss_clean']
            ]);
            
            if($form_validate){
                // post tangkap inputan
                $fail       = false;
                $is_valid   = false;                
                $data       = [];
                $mod        = post('module');

                // get module detail
                $path   = APPPATH.'modules';
                if (is_dir($path.DS.$mod) && ! empty($mod)) {
                    // cek modul yang belum ada di database
                    if(! $this->M_module->get(null, [ 'id' => strtolower($mod) ])){
                        // dapatkan meta modul
                        if(is_file($meta = $path.DS.$mod.DS.'install.txt')){
                            // baca meta modul
                            $cont   = file_get_contents($meta);
                            $ex     = explode("|", $cont);

                            // masukkan dalam list
                            if(count($ex) == 3){
                                $data   = [
                                    'id'            => $mod,
                                    'name'          => $ex[0],
                                    'created_at'    => Carbon::now(),
                                    'status'        => 1,
                                    'version'       => $ex[1],
                                    'type'          => str_replace(['App', 'Sys'], ['A', 'S'], $ex[2])
                                ];

                                // insert
                                $i = $this->M_module->insert(null, $data);
                                if(! $i){
                                    $fail = true;
                                }else{
                                    // masukkan semua access ke dalam tabel
                                    if(! $this->_insert_access($path.DS.$mod, $mod)){
                                        $fail = true;
                                    }
                                }
                            }

                        }
                    }
                }

                // cek upload data user
                // bila data tidak empty maka insert
                if(! empty($_FILES['new_module']['name'])){

                    $path       = './uploads/module';
                    $mod_path   = APPPATH.'modules';
                    
                    // masukkan ke folder sementara
                    $config = [
                        'path'  => $path,
                        'type'  => 'zip',
                        'name'  => 'temp_mod'
                    ];
                    $u = do_upload('new_module', $config);
                    if($u['status'] == 1){
                        $mod_meta = [];
                        $mod_name = null;
                        $temp_mod = [];

                        $a = $this->unzip->extract($path.DS.'temp_mod.zip');
                        if(! empty($a)){
                            $temp_mod = $a;
                        }
                        
                        foreach ($a as $row) {
                            if(strpos($row, "install.txt") !== false){
                                $mod_meta = explode("|", file_get_contents($row));
                                
                                // masukkan dalam list
                                if(count($mod_meta) == 3){
                                    $is_valid   = true;
                                }
                            }
                        }

                        if($is_valid){
                            // cek directory untuk dapatkan id modul
                            foreach(scandir($path) as $row){
                                if ($row === '.' || $row === '..') continue;
                                if (is_dir($path.DS.$row)) {
                                    $mod_name = $row;
                                }
                            }

                            // bila nama modul dan meta tidak kosong, maka ekstrak ulang ke folder modules
                            $b = $this->unzip->extract($path.DS.'temp_mod.zip', $mod_path.DS);
                            $this->unzip->close();

                            if(! empty($b)){
                                // insert data ke database
                                $data   = [
                                    'id'            => $mod_name,
                                    'name'          => $mod_meta[0],
                                    'created_at'    => Carbon::now(),
                                    'status'        => 1,
                                    'version'       => $mod_meta[1],
                                    'type'          => str_replace(['App', 'Sys'], ['A', 'S'], $mod_meta[2])
                                ];

                                // insert ke tabel
                                if(! $this->M_module->get(null, [ 'id' => $mod_name ])){
                                    $i = $this->M_module->insert(null, $data);
                                    if(! $i){
                                        $fail = true;
                                    }else{
                                        // hapus folder uploads/module
                                        $this->_delete_files($path);
                                    }

                                    // masukkan semua access ke dalam tabel
                                    if(! $this->_insert_access($mod_path.DS.$mod_name, $mod_name)){
                                        $fail = true;
                                    }
                                }else{
                                    $fail = true;                                    
                                }
                            }
                        }else{
                            $fail = true;
                        }
                    }else{
                        $fail = true;
                    }
                }

                if($i){
                    // success message 
                    flash(['GLOBAL_ALERT_SUCCESS' => 'Data Berhasil Disimpan.']);
                    redirect(back());
                }else{
                    // fail message 
                    flash(['GLOBAL_ALERT_FAIL' => 'Data Gagal Disimpan. Silakan coba beberapa saat lagi.']);
                    redirect(back());
                }
            }else{
                flash(['MSG_ERROR' => validation_errors()]);
                redirect(back());
            }
            
        }
    }

    public function detail($id_en){
        // get data sesuai dengan id ini        
        $id     = decrypt($id_en);
        $q      = $this->M_module->get(null, [ 'id' => $id ]);
        
        if(! empty($id_en) && $q){
            
            // heading table
            $this->table->set_heading(
                ['data' => 'No', 'class' => 'text-center', 'style' => 'width:8%;'],
                ['data' => 'ID Controller', 'class' => 'text-center'],         
                ['data' => 'ID Method / Access', 'class' => 'text-center']
            );
            
            // contoller dari result access
            $cur_ctrl   = null;
            $access     = $this->M_module->get('app_access', [ 'app_modules_id' => $id ]);
            foreach($access as $key => $row){
                if($cur_ctrl != $row->class_name){
                    $cur_ctrl = $row->class_name;

                    // buat rowspan baru
                    $this->table->add_row(
                        ['data' => strtoupper("CLASS: {$row->class_name}"), 'class' => 'text-bold bg-red text-center', 'colspan' => 3],
                        ['data' => "", 'class' => 'hidden'],
                        ['data' => "", 'class' => 'hidden']                    
                    );
                }
                $this->table->add_row(
                    ['data' => ++$key, 'class' => 'text-center'],
                    ['data' => strtolower($row->class_name), 'class' => 'text-center'],
                    ['data' => strtolower($row->access_name), 'class' => 'text-center']
                );
            }

            $data['id']     = $id_en;
            $data['q']      = $q;
            $data['body']   = generate_table();
            $data['priv']   = $this->user_priviledge;  
            $data['links']  = $this->_quick_actions($id_en);            
            $data['title']  = "Detail {$this->module}";
            $this->slice->view('module.detail', $data);
        }
    }

    public function edit($id_en){
        if(! post('submit')){
            // bukan post, maka tampilkan halaman edit

            // get data sesuai dengan id ini        
            $id     = decrypt($id_en);
            $q      = $this->M_module->get(null, [ 'id' => $id ]);
            
            if(! empty($id_en) && $q){
                // refresh access untuk modul ini
                $data['id']     = $id_en;
                $data['q']      = $q;
                $data['priv']   = $this->user_priviledge;
                $data['body']   = $this->_result_table();
                $data['links']  = $this->_quick_actions($id_en);                
                $data['title']  = "Edit {$this->module}";
                $this->slice->view('module.edit', $data);
            }
        }elseif(post('submit') && post('method') == '_patch'){
            // validate
            $form_validate = validation([
                ['new_module', 'Module Baru', 'xss_clean']
            ]);

            if($form_validate){
                $fail       = false;
                $is_valid   = false;                
                $data       = [];

                // cek upload data user
                // bila data tidak empty maka insert
                if(! empty($_FILES['new_module']['name'])){
                    
                    $path       = './uploads/module';
                    $mod_path   = APPPATH.'modules';
                    
                    // masukkan ke folder sementara
                    $config = [
                        'path'  => $path,
                        'type'  => 'zip',
                        'name'  => 'temp_mod'
                    ];
                    $u = do_upload('new_module', $config);
                    if($u['status'] == 1){
                        $mod_meta = [];
                        $mod_name = null;
                        $temp_mod = [];

                        $a = $this->unzip->extract($path.DS.'temp_mod.zip');
                        if(! empty($a)){
                            $temp_mod = $a;
                        }
                        
                        foreach ($a as $row) {
                            if(strpos($row, "install.txt") !== false){
                                $mod_meta = explode("|", file_get_contents($row));
                                
                                // masukkan dalam list
                                if(count($mod_meta) == 3){
                                    $is_valid   = true;
                                }
                            }
                        }

                        if($is_valid){
                            // cek directory untuk dapatkan id modul
                            foreach(scandir($path) as $row){
                                if ($row === '.' || $row === '..') continue;
                                if (is_dir($path.DS.$row)) {
                                    $mod_name = $row;
                                }
                            }

                            // bila nama modul dan meta tidak kosong, maka ekstrak ulang ke folder modules
                            $b = $this->unzip->extract($path.DS.'temp_mod.zip', $mod_path.DS);
                            $this->unzip->close();

                            if(! empty($b)){
                                // insert data ke database
                                $data   = [
                                    'name'          => $mod_meta[0],
                                    'created_at'    => Carbon::now(),
                                    'status'        => 1,
                                    'version'       => $mod_meta[1],
                                    'type'          => str_replace(['App', 'Sys'], ['A', 'S'], $mod_meta[2])
                                ];

                                // insert ke tabel
                                if($this->M_module->get(null, [ 'id' => $mod_name ])){
                                    $i = $this->M_module->update(null, [ 'id' => $mod_name ], $data);
                                    if(! $i){
                                        $fail = true;
                                    }else{
                                        // hapus folder uploads/module
                                        $this->_delete_files($path);
                                    }

                                    // masukkan semua access ke dalam tabel
                                    if(! $this->_insert_access($mod_path.DS.$mod_name, $mod_name)){
                                        $fail = true;
                                    }
                                }else{
                                    $fail = true;                                    
                                }
                            }
                        }else{
                            $fail = true;
                        }
                    }else{
                        $fail = true;
                    }

                    // success message
                }
            }else{
                flash(['MSG_ERROR' => validation_errors()]);
                redirect(back());
            }
        }

    }

    public function delete(){
        // masukkan id yang tidak ingin dihapus
        $success    = false;
        $guarded    = [ 'mgmt' ];
        $output     = json_encode([ 'status' => false ]);
        if($this->request_method_delete && ! empty($id = $this->request_data['id'])){
            // bila banyak data maka hapus satu2
            if(is_array($id)){
                foreach($id as $row){
                    if(! in_array(decrypt($row), $guarded)){
                        if($this->M_module->delete(null, [ 'id' => decrypt($row) ])){
                            $this->_delete_files($mod_path.DS.$row);
                            $success = true;
                        }
                    }
                }
            }else{
                if(! in_array(decrypt($id), $guarded)){
                    if($this->M_module->delete(null, [ 'id' => decrypt($id) ])){
                        $this->_delete_files($mod_path.DS.$id);
                        $success = true;
                    }
                }
            }

            if($success){
                // hapus di folder module untuk id ini
                $mod_path   = APPPATH.'modules';
                $output = json_encode([ 'status' => true, 'html' => $this->_result_table() ]);
            }
        }

        echo $output;
    }

    function _quick_actions($id){
        // ini untuk menu tambahan seperti import dll yang digunakan disemua method
        // bila mthod punya link yang berbeda bisa didefinisikan sendiri dimasing-masing
        // method.
        $links = [];
        if($this->user_priviledge->add == 1 && method_exists($this, 'create') && access()->method != 'create'){            
            array_push(
                $links, anchor(str_replace('/edit/'.$id, '/create', base_url(uri_string())), '<i class="fa fa-plus"></i> Entri Baru')
            );
        }if($this->user_priviledge->detail == 1 && method_exists($this, 'detail') && access()->method != 'detail'){            
            array_push(
                $links, anchor(str_replace('/edit/', '/detail/', base_url(uri_string())), '<i class="fa fa-eye"></i> Detail Entri')
            );
        }if($this->user_priviledge->delete == 1 && method_exists($this, 'delete') && access()->method != 'delete'){
            array_push(
                $links, anchor(base_url(uri_string()), '<i class="fa fa-trash"></i> Hapus Entri', 'class="btn-erase-single text-red" data-url="'.base_url(uri_string().'/delete').'" data-redirect="'.str_replace('/edit/'.$id, '', base_url(uri_string())).'" data-id="'.$id.'"')
            );
        }

        return $links;
    }

    function _result_table(){
        $this->table->set_heading(
            ['data' => '<input type="checkbox" class="check-all icheck" />', 'class' => 'no-sort', 'style' => 'width:20px;'], 
            ['data' => 'No', 'class' => 'text-center', 'style' => 'width:8%;'],
            ['data' => 'Nama Module'],         
            ['data' => 'Access', 'class' => 'text-center'],            
            ['data' => 'Action', 'class' => 'text-center', 'style' => 'width:14%;']
        );
        
        // get semua entry role
        $role = $this->M_module->get(null, []);
        foreach($role as $key => $row){
            
            // tombol action
            $action = generate_actions([
                'detail'    => anchor(base_url(uri_string().'/detail/'.encrypt($row->id)), '<i class="fa fa-eye"></i> Lihat Entri', 'target="_blank"'),
                'edit'      => anchor(base_url(uri_string().'/edit/'.encrypt($row->id)), '<i class="fa fa-refresh"></i> Update Entri', 'target="_blank"'),
                'delete'    => anchor(base_url(uri_string()), '<i class="fa fa-trash"></i> Hapus Entri', 'class="btn-erase-single text-red" data-url="'.base_url(uri_string().'/delete').'" data-id="'.encrypt($row->id).'"'),
            ], $this->user_priviledge, $this);

            $created_at = Carbon::parse($row->created_at)->diffForHumans();
            $type       = ($row->type == 'S') ? 'System' : 'Application';

            // dapatkan jumlah access per modul
            $access     = $this->M_module->get_count('app_access', [ 'app_modules_id' => $row->id ]);

            $this->table->add_row(
                ['data' => '<input type="checkbox" class="icheck check-all-child" data-id="'.encrypt($row->id).'" />'],
                ['data' => ++$key, 'class' => 'text-center'],
                ['data' => "<b>{$row->name}</b><span class='clearfix'>ID: <span class='text-primary'>{$row->id} &middot; v{$row->version} &middot; {$type}</span></span><span class='clearfix'>Terdaftar: <span class='text-primary'>{$created_at}</span></span>"],
                ['data' => $access, 'class' => 'text-center'],
                ['data' => $action, 'class' => 'text-center']
            );
        }

        return generate_table();
    }

    function _filter(){
        $filter = null;
        $data = [
            'type'      => 'text',
            'name'      => 'query',
            'class'     => 'form-control'            
        ];

        $data2 = [
            'opt1'      => 'AAAA',
            'opt2'      => 'AAAA',
            'opt3'      => 'AAAA'
        ];
        
        $filter = filter_form([
            'Dropdown'  => form_dropdown('dropdown', $data2, null, 'class="form-control selectpicker" data-live-search="true"'),
            'Keyword'   => form_input($data, null, 'placeholder="Kata Kunci"')
        ]);

        return $filter;
    }

    function _delete_files($target) {
        if(is_dir($target)){
            $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
            
            foreach($files as $file){
                $this->_delete_files($file);
            }
          
            @rmdir($target);
        } elseif(is_file($target)) {
            @unlink($target);  
        }
    }

    function _insert_access($path, $module_name){
        
        $fail        = false;

        // pa
        $ctrl_path   = $path.DS."controllers";
        $ctrl_folder = array_diff(scandir($ctrl_path), array('.', '..', 'index.html'));
        foreach ($ctrl_folder as $controller) {

            // ubah nama controller.php ke controller saja untuk dimasukkan ke database
            $ctrl_name  = strtolower(str_replace(".php", "", $controller));
            $methods    = $this->_get_functions($ctrl_path.DS.$controller);
            if($methods){
                foreach($methods as $method){
                    // ignore __construct dan semua private function
                    if($method != '__construct' && substr($method,0,1) != '_' && $method != 'if'){
                        // insert ke dalam database
                        $data = [
                            'app_modules_id'    => $module_name,
                            'class_name'        => $ctrl_name,
                            'access_name'       => $method
                        ];

                        // cek apakah access ini sudah ada atau blum
                        // bila blum ada, maka insert
                        if(! $this->M_module->get('app_access', $data)){
                            $i = $this->M_module->insert('app_access', $data);
                            if(! $i){
                                $fail = true;
                            }
                        }
                    }
                }
            }
        }

        if($fail){
            return false;
        }else{
            return true;
        }
    }

    function _get_functions($file) {
        $functionFinder = '/function[\s\n]+(\S+)[\s\n]*\(/';
        $functionArray  = array();
        $fileContents   = file_get_contents($file);

        preg_match_all( $functionFinder , $fileContents , $functionArray );
        if( count( $functionArray ) > 1 ){
            $functionArray = $functionArray[1];
        }
        return $functionArray;
    }
}