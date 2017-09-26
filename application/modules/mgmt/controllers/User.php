<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

class User extends Admin_Controller {

    protected $module   = "User";

    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $data['priv']   = $this->user_priviledge;
        $data['body']   = $this->_result_table();
        $data['title']  = "Management {$this->module}";
        $this->slice->view('user.index', $data);
    }

    public function create(){
        if(! post('submit')){
            // bukan post, maka tampilkan halaman create
            
            // generate all role
            $data2  = [];
            $q      = $this->M_role->get(null, []);
            foreach($q as $row){
                $data2 += [ $row->id => $row->name ];
            }

            $data['role']   = form_dropdown('role', $data2, null, 'class="form-control selectpicker" data-live-search="true"');
            $data['priv']   = $this->user_priviledge;            
            $data['body']   = $this->_result_table();
            $data['links']  = [anchor(str_replace('/create', '/import', base_url(uri_string())), '<i class="fa fa-file"></i> Import Data')];    
            $data['title']  = "Tambah {$this->module}";
            $this->slice->view('user.create', $data);
        }else{
            // validate
            $form_validate = validation([
                ['nama', 'Nama Lengkap', 'trim|required'],
                ['email', 'Email', 'trim|required'],
                ['role', 'Role', 'required'],
                ['status', 'Status User', 'required'],
                ['confirm', 'Konfirmasi Email', 'required'],
                ['password', 'Password', 'required|xss_clean'],
                ['password_confirmation', 'Konfirmasi Password', 'required|xss_clean|matches[password]']
            ]);

            // email sudah digunakan
            if($this->M_registration->get('app_users', [ 'email' => post('email') ])){
                flash(['MSG_ERROR' => "Email ini sudah digunakan."]);
                redirect(back());
            }
            
            if($form_validate){
                // apakah user baru harus konfirmasi email?
                // bila settingan mengharuskan maka pending statusnya dan kirim email ke user

                $q = $this->M_registration->create(post(), post('role'), post('status'));
                if(! empty($q)){

                    // kirim email konfirmasi ke user baru
                    if(app()->register_validate && post('conirm') == 1){
                        if(! empty($token = $this->M_session->forget($q, 'P'))){

                            $msg = "Confirm User Here: ".base_url('/confirm/'.$token);
                            send_email("Confirm Email", $msg, null, post('email'));

                        }
                    }
                    
                    // success message 
                    flash(['GLOBAL_ALERT_SUCCESS' => 'Akun Berhasil Dibuat.']);
                    redirect(back());
                }else{
                    // fail message 
                    flash(['GLOBAL_ALERT_FAIL' => 'Akun Gagal Dibuat, Silakan Ulangi Lagi.']);
                    redirect(back());
                }
            }else{
                flash(['MSG_ERROR' => validation_errors()]);
                redirect(back());
            }
            
        }
    }

    public function import(){
        if(! post('submit')){
            $data['priv']   = $this->user_priviledge;
            $data['links']  = [anchor(str_replace('/import', '/create', base_url(uri_string())), '<i class="fa fa-file"></i> Import Data')];    
            $data['title']  = "Import {$this->module}";
            $this->slice->view('user.import', $data);
        }else{
            // post
        }
    }

    public function detail($id_en){
        if(! empty($id_en)){
            redirect(base_url('/profile/'.$id_en));
        }
    }

    public function edit($id_en){
        if(! post('submit')){
            // bukan post, maka tampilkan halaman edit
            // get data sesuai dengan id ini
            $id     = decrypt($id_en);
            $q      = $this->M_user->get(null, [ 'id' => $id ]);

            if(! empty($id_en) && $q){
                // generate all role
                $data2  = [];
                $data3  = [
                    1 => 'AKTIF',
                    2 => 'PENDING',
                    0 => 'NON-AKTIF'
                ];
                $role   = $this->M_role->get(null, []);
                foreach($role as $row){
                    $data2 += [ $row->id => $row->name ];
                }

                $data['id']     = $id_en;
                $data['q']      = $q;
                $data['role']   = form_dropdown('role', $data2, $q[0]->app_role_id, 'class="form-control selectpicker" data-live-search="true"');
                $data['status'] = form_dropdown('status', $data3, $q[0]->status, 'class="form-control selectpicker" data-live-search="true"');
                $data['links']  = $this->_quick_actions($id_en);                
                $data['priv']   = $this->user_priviledge;
                $data['body']   = $this->_result_table();
                $data['title']  = "Edit {$this->module}";
                $this->slice->view('user.edit', $data);
            }
        }elseif(post('submit') && post('method') == '_patch'){
            // get data sesuai dengan id ini
            $id = decrypt($id_en);
            $q  = $this->M_user->get(null, [ 'id' => $id ]);

            if(! empty($id_en) && $q){
                $form_validate = validation([
                    ['nama', 'Nama Lengkap', 'trim|required'],
                    ['email', 'Email', 'trim|required'],
                    ['role', 'Role', 'required'],
                    ['status', 'Status User', 'required'],
                    ['password', 'Password', 'xss_clean']
                ]);
                
                if($form_validate){
                    $data = [];

                    // ganti password apabila post password tidak kosong
                    if(! empty(post('password'))){
                        // generate salt & combine with inputed password
                        $salt       = random_string('alnum', 128);
                        $password   = hash("sha512", post('password').$salt, FALSE);

                        // reset passord data
                        $data += [
                            'password'  => $password,
                            'salt'      => $salt
                        ];
                    }

                    // data selain password
                    $data += [
                        'full_name'     => post('nama'),
                        'email'         => post('email'),
                        'app_role_id'   => post('role'),
                        'status'        => post('status'),
                        
                    ];
    
                    // insert ke table
                    $i = $this->M_user->update(null, [ 'id' => $id ], $data);
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
    }

    public function delete(){
        // masukkan id yang tidak ingin dihapus
        $success    = false;
        $guarded    = [ $this->user_data->user_id ];
        $output     = json_encode([ 'status' => false ]);
        if($this->request_method_delete && ! empty($id = $this->request_data['id'])){
            // bila banyak data maka hapus satu2
            if(is_array($id)){
                foreach($id as $row){
                    if(! in_array(decrypt($row), $guarded)){
                        if($this->M_user->delete(null, [ 'id' => decrypt($row) ])){
                            $success = true;
                        }
                    }
                }
            }else{
                if(! in_array(decrypt($id), $guarded)){
                    if($this->M_user->delete(null, [ 'id' => decrypt($id) ])){
                        $success = true;
                    }
                }
            }

            if($success){
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
            ['data' => 'Nama User'],
            ['data' => 'Role', 'class' => 'text-center'],
            ['data' => 'Status', 'class' => 'text-center'],
            ['data' => 'Action', 'class' => 'text-center', 'style' => 'width:14%;']
        );
        
        // get semua entry role
        $role = $this->M_user->get(null, []);
        foreach($role as $key => $row){

            // email
            $email      = (isset($row->email)) ? safe_mailto($row->email) : 'N/A';
            $registered = Carbon::parse($row->created_at)->diffForHumans();
            $role       = ($r = $this->M_role->get(null, [ 'id' => $row->app_role_id ])) ? ucwords($r[0]->name) : 'N/A';
            $status     = ($row->status == 1) ? "<label class='label label-success'>AKTIF</label>" : "<label class='label label-default'>INAKTIF</label>";

            // tombol action
            $action = generate_actions([
                'detail'    => anchor(base_url(uri_string().'/detail/'.encrypt($row->id)), '<i class="fa fa-eye"></i> Lihat Entri', 'target="_blank"'),
                'edit'      => anchor(base_url(uri_string().'/edit/'.encrypt($row->id)), '<i class="fa fa-edit"></i> Edit Entri', 'target="_blank"'),
                'delete'    => anchor(base_url(uri_string()), '<i class="fa fa-trash"></i> Hapus Entri', 'class="btn-erase-single text-red" data-url="'.base_url(uri_string().'/delete').'" data-id="'.encrypt($row->id).'"'),
            ], $this->user_priviledge, $this);

            // bila user sendiri jangan tampilkan action
            if($this->user_data->user_id == $row->id){
                $action = '<em>Tidak Ada Action</em>';
            }

            $this->table->add_row(
                ['data' => '<input type="checkbox" class="icheck check-all-child" data-id="'.encrypt($row->id).'" />'],
                ['data' => ++$key, 'class' => 'text-center'],
                ['data' => "<b>{$row->full_name}</b><span class='clearfix'>Email: {$email}</span><span class='clearfix'>Terdaftar: <span class='text-primary'>{$registered}</span></span>"],
                ['data' => "<span class='text-bold text-primary'>{$role}</span>", 'class' => 'text-center'],
                ['data' => $status, 'class' => 'text-center'],
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
}