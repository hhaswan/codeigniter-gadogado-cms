<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

class Role extends Admin_Controller {
        
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){

        $data['body']   = $this->_result_table();
        $data['title']  = "Management Role";
        $this->slice->view('role.index', $data);
    }

    public function delete(){
        // masukkan id yang tidak ingin dihapus
        $success    = false;
        $guarded    = [1, 2];
        $output     = json_encode([ 'status' => false ]);
        if($this->request_method_delete && ! empty($id = $this->request_data['id'])){
            // bila banyak data maka hapus satu2
            if(is_array($id)){
                foreach($id as $row){
                    if(! in_array($row, $guarded)){
                        if(! $this->M_role->delete(null, [ 'id' => $row ])){
                            $success = true;
                        }
                    }
                }
            }else{
                if(! in_array($id, $guarded)){
                    if(! $this->M_role->delete(null, [ 'id' => $id ])){
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
                'view'      => anchor(base_url(uri_string().'/detail/'.$row->id), '<i class="fa fa-eye"></i> Lihat Entri', 'target="_blank"'),
                'edit'      => anchor(base_url(uri_string().'/edit/'.$row->id), '<i class="fa fa-edit"></i> Edit Entri', 'target="_blank"'),
                'delete'    => anchor(base_url(uri_string()), '<i class="fa fa-trash"></i> Hapus Entri', 'class="btn-erase-single text-red" data-url="'.base_url(uri_string().'/delete').'" data-id="'.$row->id.'"'),
            ]);

            $this->table->add_row(
                ['data' => '<input type="checkbox" class="icheck check-all-child" data-id="'.$row->id.'" />'],
                ['data' => ++$key, 'class' => 'text-center'],
                ['data' => "<b>{$row->name}</b><span class='clearfix'>Alias: {$row->alias}</span>"],           
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
