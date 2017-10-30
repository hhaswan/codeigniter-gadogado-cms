<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

class Division extends Admin_Controller {

    protected $module   = "Divisi";

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
            $data4  = [];
            $divisi = $this->M_division->get(null, ['is_access_all' => 0]);
            foreach ($divisi as $value) {
                // masukkan yang tidak memiliki akses ke semua divisi
                $data4 += [$value->id => ucwords($value->name)];
            }
            
            $data['area']   = form_dropdown('area[]', $data4, null, 'class="form-control selectpicker" data-live-search="true" multiple');
            $data['priv']   = $this->user_priviledge;            
            $data['body']   = $this->_result_table();
            $data['title']  = "Tambah {$this->module}";
            $this->slice->view('division.create', $data);
        }else{
            // validate
            $form_validate = validation([
                ['nama', 'Nama Divisi', 'required|xss_clean'],
                ['access', 'Level Akses', 'required']
            ]);
            
            if($form_validate){
                // post tangkap inputan
                $data   = [
                    'name'          => post('nama'),
                    'is_access_all' => post('access')
                ];

                // insert ke table
                $i = $this->M_division->insert(null, $data);
                if($i){
                    // masukkan area akses untuk divisi ini
                    // masukkan divisi dirinya sendiri
                    $data_a = [
                        'app_divisions_id'  => $i,
                        'division_access'   => $i
                    ];
                    $this->M_division->insert_access_division($data_a);

                    if(! empty(post('area')) && post('access') != 1){
                        // insert baru diluar divisinya sendiri
                        foreach (post('area') as $value) {
                            $data_a = [
                                'app_divisions_id'  => $i,
                                'division_access'   => $value
                            ];
                            $this->M_division->insert_access_division($data_a);
                        }
                    }

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
            $q      = $this->M_division->get(null, [ 'id' => $id ]);

            if(! empty($id_en) && $q){
                $data3  = [
                    0 => 'NORMAL',
                    1 => 'AKSES SEMUA DATA'
                ];

                $data4  = [];
                $divisi = $this->M_division->get(null, ['id !=' => $id, 'is_access_all' => 0]);
                foreach ($divisi as $value) {
                    // masukkan yang tidak memiliki akses ke semua divisi
                    $data4 += [$value->id => ucwords($value->name)];
                }

                // get semua akses divisi ini
                $selected = [];

                $da = $this->M_division->get_access_division(['app_divisions_id' => $id]);
                foreach ($da as $value) {
                    array_push($selected, $value->division_access);
                }

                $data['access'] = form_dropdown('access', $data3, $q[0]->is_access_all, 'class="form-control selectpicker" id="access-sel"');
                $data['area']   = form_dropdown('area[]', $data4, $selected, 'class="form-control selectpicker" data-live-search="true" multiple');
                $data['id']     = $id_en;
                $data['q']      = $q;
                $data['links']  = $this->_quick_actions($id_en);                
                $data['priv']   = $this->user_priviledge;
                $data['body']   = $this->_result_table();
                $data['title']  = "Edit {$this->module}";
                $this->slice->view('division.edit', $data);
            }
        }elseif(post('submit') && post('method') == '_patch'){
            // get data sesuai dengan id ini
            $id = decrypt($id_en);
            $q  = $this->M_division->get(null, [ 'id' => $id ]);

            if(! empty($id_en) && $q){
                $form_validate = validation([
                    ['nama', 'Nama Divisi', 'required|xss_clean'],
                    ['access', 'Level Akses', 'required']
                ]);
                
                if($form_validate){
                    $data   = [
                        'name'          => post('nama'),
                        'is_access_all' => post('access')
                    ];

                    // bila area tidak dipilih maka update tabel division_access
                    // untuk divisi ini
                    
                    // hapus semua area akses untuk divisi ini
                    $this->M_division->delete_access_division(['app_divisions_id' => $id]);

                    if(! empty(post('area')) && post('access') != 1){    
                        // masukkan divisi dirinya sendiri
                        $data_a = [
                            'app_divisions_id'  => $id,
                            'division_access'   => $id
                        ];
                        $this->M_division->insert_access_division($data_a);

                        // insert baru diluar divisinya sendiri
                        foreach (post('area') as $value) {
                            $data_a = [
                                'app_divisions_id'  => $id,
                                'division_access'   => $value
                            ];
                            $this->M_division->insert_access_division($data_a);
                        }
                    }elseif(empty(post('area'))){
                        if(post('access') != 1){
                            // masukkan divisi dirinya sendiri
                            $data_a = [
                                'app_divisions_id'  => $id,
                                'division_access'   => $id
                            ];
                            $this->M_division->insert_access_division($data_a);
                        }
                    }
    
                    // insert ke table
                    $i = $this->M_division->update(null, [ 'id' => $id ] ,$data);
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
                        if($this->M_division->delete(null, [ 'id' => decrypt($row) ])){
                            $success = true;
                        }
                    }
                }
            }else{
                if(! in_array(decrypt($id), $guarded)){
                    if($this->M_division->delete(null, [ 'id' => decrypt($id) ])){
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
            ['data' => 'Level Akses', 'class' => 'text-center'],
            ['data' => 'Area Akses', 'class' => 'text-center', 'style' => 'width:30%;'],
            ['data' => 'Action', 'class' => 'text-center', 'style' => 'width:14%;']
        );
        
        // get semua entry
        $role = $this->M_division->get(null, []);
        foreach($role as $key => $row){
            
            // tombol action
            $action = generate_actions([
                'detail'    => anchor(base_url(uri_string().'/detail/'.encrypt($row->id)), '<i class="fa fa-eye"></i> Lihat Entri', 'target="_blank"'),
                'edit'      => anchor(base_url(uri_string().'/edit/'.encrypt($row->id)), '<i class="fa fa-edit"></i> Edit Entri', 'target="_blank"'),
                'delete'    => anchor(base_url(uri_string()).'#', '<i class="fa fa-trash"></i> Hapus Entri', 'class="btn-erase-single text-red" data-url="'.base_url(uri_string().'/delete').'" data-id="'.encrypt($row->id).'"'),
            ], $this->user_priviledge, $this);

            // label akses semua
            $acc_all = ($row->is_access_all == 1) ? '<label class="label label-primary" data-toggle="tooltip" title="Dapat mengakses data dari semua divisi"><i class="fa fa-check"></i> Akses Semua Data</label>' : '<label class="label label-default" data-toggle="tooltip" title="Hanya bisa akses data sesuai atau beberapa divisi saja"><i class="fa fa-check"></i> Akses Normal</label>';

            // daftar akses
            $div_akses = [];
            $da = $this->M_division->get_access_division(['app_divisions_id' => $row->id]);
            foreach($da as $row_ac){
                if($aa = $this->M_division->get(null, ['id' => $row_ac->division_access])){
                    array_push($div_akses, $aa[0]->name);
                }
            }

            // data akses
            $d_akses = implode(", ", $div_akses);

            if(empty($d_akses) && $row->is_access_all == 1){
                $d_akses = '<em>Semua Area Akses</em>';
            }elseif(empty($d_akses) && $row->is_access_all != 1){
                $d_akses = 'N/A';
            }

            $this->table->add_row(
                ['data' => '<input type="checkbox" class="icheck check-all-child" data-id="'.encrypt($row->id).'" />'],
                ['data' => ++$key, 'class' => 'text-center'],
                ['data' => "<b>{$row->name}</b>"],
                ['data' => $acc_all, 'class' => 'text-center'],
                ['data' => $d_akses, 'class' => 'text-center'],
                ['data' => $action, 'class' => 'text-center']
            );
        }

        return generate_table();
    }
}