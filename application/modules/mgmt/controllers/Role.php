<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

class Role extends Admin_Controller {

    protected $module   = "Role";

    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $data['action'] = [
            anchor(base_url(uri_string().'/create'), '<i class="fa fa-plus"></i> <span class="hidden-xs">Tambah Data</span>', 'class="btn btn-primary btn-sm"'),
            anchor(base_url(uri_string().'#'), '<i class="fa fa-trash"></i> <span class="hidden-xs">Hapus Data</span>>', 'class="btn btn-danger btn-sm btn-erase" data-url="'.base_url(uri_string().'/delete').'" disabled'),
        ];
        $data['priv']   = $this->user_priviledge;
        $data['body']   = $this->_result_table();
        $data['title']  = "Management {$this->module}";
        $this->slice->view('commons.index_master', $data);
    }

    public function create(){
        if(! post('submit')){
            // bukan post, maka tampilkan halaman create
            $data['priv']   = $this->user_priviledge;            
            $data['body']   = $this->_result_table();
            $data['title']  = "Tambah {$this->module}";
            $this->slice->view('role.create', $data);
        }else{
            // validate
            $form_validate = validation([
                ['name', 'Nama Role', 'required'],
                ['alias', 'Alias Role', 'xss_clean']
            ]);
            
            if($form_validate){
                // post tangkap inputan
                if(! empty(post('alias'))){
                    $alias  = url_title(strtolower(post('alias')), 'underscore');
                }else{
                    $alias  = url_title(strtolower(post('name')), 'underscore');
                }
                $data   = [
                    'name'  => post('name'),
                    'alias' => $alias
                ];

                // insert ke table
                $i = $this->M_role->insert(null, $data);
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

    public function edit($id_en){
        if(! post('submit')){
            // bukan post, maka tampilkan halaman edit
            // get data sesuai dengan id ini
            $id     = decrypt($id_en);
            $q      = $this->M_role->get(null, [ 'id' => $id ]);

            if(! empty($id_en) && $q){
                $data['id']     = $id_en;
                $data['q']      = $q;
                $data['links']  = $this->_quick_actions($id_en);                
                $data['priv']   = $this->user_priviledge;
                $data['body']   = $this->_result_table();
                $data['title']  = "Edit {$this->module}";
                $this->slice->view('role.edit', $data);
            }

        }elseif(post('submit') && post('method') == '_patch'){
            // get data sesuai dengan id ini
            $id = decrypt($id_en);
            $q  = $this->M_role->get(null, [ 'id' => $id ]);

            if(! empty($id_en) && $q){
                $form_validate = validation([
                    ['name', 'Nama Role', 'required'],
                    ['alias', 'Alias Role', 'xss_clean']
                ]);
                
                if($form_validate){
                    // post tangkap inputan
                    if(! empty(post('alias'))){
                        $alias  = url_title(strtolower(post('alias')), 'underscore');
                    }else{
                        $alias  = url_title(strtolower(post('name')), 'underscore');
                    }
                    $data   = [
                        'name'  => post('name'),
                        'alias' => $alias
                    ];
    
                    // insert ke table
                    $i = $this->M_role->update(null, [ 'id' => $id ] ,$data);
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
        $guarded    = [1];
        $output     = json_encode([ 'status' => false ]);
        if($this->request_method_delete && ! empty($id = $this->request_data['id'])){
            // bila banyak data maka hapus satu2
            if(is_array($id)){
                foreach($id as $row){
                    if(! in_array(decrypt($row), $guarded)){
                        if($this->M_role->delete(null, [ 'id' => decrypt($row) ])){
                            $success = true;
                        }
                    }
                }
            }else{
                if(! in_array(decrypt($id), $guarded)){
                    if($this->M_role->delete(null, [ 'id' => decrypt($id) ])){
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
            ['data' => 'Nama Role'],         
            ['data' => 'Action', 'class' => 'text-center', 'style' => 'width:14%;']
        );
        
        // get semua entry role
        $role = $this->M_role->get(null, []);
        foreach($role as $key => $row){
            
            // tombol action
            $action = generate_actions([
                'detail'    => anchor(base_url(uri_string().'/detail/'.encrypt($row->id)), '<i class="fa fa-eye"></i> Lihat Entri', 'target="_blank"'),
                'edit'      => anchor(base_url(uri_string().'/edit/'.encrypt($row->id)), '<i class="fa fa-edit"></i> Edit Entri', 'target="_blank"'),
                'delete'    => anchor(base_url(uri_string()), '<i class="fa fa-trash"></i> Hapus Entri', 'class="btn-erase-single text-red" data-url="'.base_url(uri_string().'/delete').'" data-id="'.encrypt($row->id).'"'),
            ], $this->user_priviledge, $this);

            $this->table->add_row(
                ['data' => '<input type="checkbox" class="icheck check-all-child" data-id="'.encrypt($row->id).'" />'],
                ['data' => ++$key, 'class' => 'text-center'],
                ['data' => "<b>{$row->name}</b><span class='clearfix'>Alias: {$row->alias}</span>"],           
                ['data' => $action, 'class' => 'text-center']
            );
        }

        return generate_table();
    }
}