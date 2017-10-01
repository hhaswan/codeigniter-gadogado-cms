<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

class Permission extends Admin_Controller {

    protected $module   = "Permission";

    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $data['priv']   = $this->user_priviledge;
        $data['body']   = $this->_result_table();
        $data['title']  = "Management {$this->module}";
        $this->slice->view('permission.index', $data);
    }

    public function edit($id_en){
        
        $id = decrypt($id_en);
        
        if(! post('submit')){
            // bukan post, maka tampilkan halaman create
            $q = $this->M_role->get(null, [ 'id' => $id ]);

            if(! empty($id_en) && $q){
                $action_head    = [
                    ['data' => '<input type="checkbox" class="check-all icheck" />', 'class' => 'no-sort', 'style' => 'width:20px;'],        
                    ['data' => 'No', 'class' => 'text-center', 'style' => 'width:8%;'],        
                    ['data' => 'ID Method / Access', 'class' => 'text-center']
                ];

                $field          = $this->M_permission->get_field_data(null);
                foreach($field as $row){
                    if($row->name != 'app_roles_id' && $row->name != 'app_access_id' && $row->name != 'id'){
                        array_push($action_head, ['data' => ucwords(humanize($row->name)), 'class' => 'text-center']);
                    }
                }
                
                // generate all role
                $this->table->set_heading($action_head);

                // apakah ada data filter?
                $module_id  = (! empty(get('module'))) ? decrypt(get('module')) : null;

                if(! empty($module_id)){
                    $filter_data = [ 'app_modules_id' => $module_id ];
                }else{
                    $filter_data = [];                
                }

                // contoller dari result access
                $cur_ctrl   = null;
                $access     = $this->M_module->get('app_access', $filter_data);
                foreach($access as $key => $row){
                    
                    // cek permissionnya dan valuenya di database
                    $pmsn   = $this->M_permission->get(null, [ 'app_access_id' => $row->id, 'app_roles_id' => $id ]);

                    $permission_avail = (! empty($pmsn)) ? 'checked' : null; 
                
                    $action_content = [
                        ['data' => '<input type="checkbox" class="icheck check-all-child check-action" name="permission['.encrypt($row->id).']" data-id="'.encrypt($row->id).'" '.$permission_avail.'/>'],            
                        ['data' => ++$key, 'class' => 'text-center'],
                        ['data' => strtolower($row->access_name), 'class' => 'text-center']
                    ];

                    foreach($field as $row_field){
                        if($row_field->name != 'app_roles_id' && $row_field->name != 'app_access_id' && $row_field->name != 'id'){

                            // cek permission pivilege di database
                            $name_field = $row_field->name;
                            if(isset($pmsn[0]->$name_field) && $pmsn[0]->$name_field == 1){
                                $permission_avail   = '<input type="checkbox" name="'.$row_field->name.'['.encrypt($row->id).']" class="icheck action-child" data-id="'.encrypt($row->id).'" checked />';
                                $style              = null;
                            }elseif(isset($pmsn[0]->$name_field) && $pmsn[0]->$name_field == 0){
                                $permission_avail   = '<input type="checkbox" name="'.$row_field->name.'['.encrypt($row->id).']" class="icheck action-child" data-id="'.encrypt($row->id).'"/>';
                                $style              = null;
                            }else{
                                $permission_avail   = '<input type="checkbox" name="'.$row_field->name.'['.encrypt($row->id).']" class="icheck action-child" data-id="'.encrypt($row->id).'" disabled />';                                
                                $style              = 'background:#EEE';
                            }
                    
                            array_push($action_content, 
                            [
                                'data'  => $permission_avail, 
                                'class' => 'text-center child_'.encrypt($row->id),
                                'style' => $style
                            ]);
                        }
                    }

                    if($cur_ctrl != $row->class_name){
                        $cur_ctrl = $row->class_name;
                        
                        // get nama modul
                        $modul      = $this->M_module->get(null, [ 'id' => $row->app_modules_id ]);
                        $nama_modul = (isset($modul)) ? $modul[0]->name : 'N/A';
                        $class_name = ucwords($row->class_name);
                        
                        // hidden col, needed to make datatable works
                        $action_hidden  = [
                            ['data' => "<span class='pull-left'>Module {$nama_modul}<br/><small class='pull-left'>Class: {$class_name}</small></span>", 'class' => 'text-bold bg-primary', 'colspan' => 7],
                            ['data' => "", 'class' => 'hidden'],
                            ['data' => "", 'class' => 'hidden']
                        ];

                        foreach($field as $row_field){
                            if($row_field->name != 'app_roles_id' && $row_field->name != 'app_access_id' && $row_field->name != 'id'){
                                array_push($action_hidden, ['data' => "", 'class' => 'hidden']);
                            }
                        }

                        // buat rowspan baru
                        $this->table->add_row($action_hidden);
                    }
                    
                    $this->table->add_row($action_content);
                }

                $data['filter'] = $this->_filter(get());
                $data['id']     = $id_en;
                $data['q']      = $q;
                $data['body']   = generate_table();
                $data['priv']   = $this->user_priviledge;
                $data['links']  = [anchor(str_replace('/edit/'.$id_en, '/import', base_url(uri_string())), '<i class="fa fa-file"></i> Import Data')];    
                $data['title']  = "Edit {$this->module}";
                $this->slice->view('permission.edit', $data);
            }
        }elseif(post('submit') && post('method') == '_patch'){
            
            $fail       = false;
            $actions    = [];
            $access_id  = [];
            $permission = post('permission');

            if(! empty($permission) && ! empty($id_en) && ! empty($id)){

                // dapatkan actions dalam tabel database
                $field = $this->M_permission->get_field_data(null);
                foreach($field as $row){
                    if($row->name != 'app_access_id' && $row->name != 'app_roles_id' && $row->name != 'id'){
                        array_push($actions, $row->name);
                    }
                }

                // cek semua post dan access yang dicentang oleh user
                foreach($permission as $key => $row){
                    
                    // masukkan access id ke array untuk discan nantinya
                    array_push($access_id, decrypt($key));

                    if(! $q = $this->M_permission->get(null, [ 'app_access_id' => decrypt($key), 'app_roles_id' => $id ]) ){
                        // insert
                        $data = [
                            'app_access_id' => decrypt($key),
                            'app_roles_id'  => $id
                        ];

                        foreach($actions as $val){
                            if(isset(post($val)[$key])){
                                $data += [ $val => 1 ];
                            }else{
                                $data += [ $val => 0 ];                                
                            }
                        }
                        
                        $i = $this->M_permission->insert(null, $data);
                        if(! $i) { $fail = true; }
                    }else{
                        // update
                        $data = [];
                        foreach($actions as $val){
                            if(isset(post($val)[$key])){
                                $data += [ $val => 1 ];
                            }else{
                                $data += [ $val => 0 ];                                
                            }
                        }
                        
                        $i = $this->M_permission->update(null, [ 'id' => $q[0]->id ], $data);
                        if(! $i) { $fail = true; }
                    }
                }

                // data permission untuk role ini yang blum ada d database, hapus
                $pms = $this->M_permission->get(null, [ 'app_roles_id' => $id ]);
                foreach($pms as $row){
                    if(! in_array($row->app_access_id, $access_id)){
                        $d = $this->M_permission->delete(null, [ 'id' => $row->id ]);
                        if(! $d) { $fail = true; }
                    }
                }

                if(! $fail){
                    // success message 
                    flash(['GLOBAL_ALERT_SUCCESS' => 'Data Berhasil Disimpan.']);
                    redirect(back());
                }else{
                    // fail message 
                    flash(['GLOBAL_ALERT_FAIL' => 'Data Gagal Disimpan. Silakan coba beberapa saat lagi.']);
                    redirect(back());                    
                }
            }          
        }
    }

    public function import($token = null){
        if(! post('submit') && empty($token)){
            $data['priv']   = $this->user_priviledge;
            $data['links']  = [anchor(str_replace('/import', '/create', base_url(uri_string())), '<i class="fa fa-plus"></i> Tambah Data')];
            $data['title']  = "Import {$this->module}";
            $this->slice->view('commons.import', $data);
        }elseif(post('submit')){
            
            $fail = false;
            
            // cek upload data user
            // bila data tidak empty maka insert
            if(! empty($_FILES['import_data']['name'])){
                
                $name       = 'data_import_'.random_string('alnum', 8);
                $path       = './uploads/import/'.strtolower($this->module);
                
                // masukkan ke folder sementara
                $config = [
                    'path'  => $path,
                    'type'  => 'xls',
                    'name'  => $name
                ];

                $u = do_upload('import_data', $config);
                if($u['status'] == 1){
                    // redirect ke import_preview
                    $link = str_replace('/import', '/import_preview/'.encrypt($name), base_url(uri_string()));
                    redirect($link);
                }else{
                    $fail = true;                    
                }
            }else{
                $fail = true;
            }

            if($fail){
                flash(['GLOBAL_ALERT_FAIL' => 'Data Gagal Disimpan. Silakan coba beberapa saat lagi.']);
                redirect(back());
            }
        }elseif($token == 'format'){
            // generate format excel untuk import

            // masukkan list field yang tidak ingin dimasukkan dalam format excel
            $excluded   = [ 'id' ];

            // PHPExcel
            $objPHPExcel = new PHPExcel();

            // array untuk border
            $styleArray = [ 'borders' => [ 'allborders' => [ 
                'style' => PHPExcel_Style_Border::BORDER_THIN ] 
                ]
            ];
            
            // Buat sheet baru excel
            $sheet = $objPHPExcel->createSheet(0);
            $sheet->setTitle("DATA ".strtoupper($this->module));
            $sheet->SetCellValue('A1', "FORMAT IMPORT ".strtoupper($this->module)." - ".strtoupper(app()->name)."\n".strtoupper(app()->company));
            $sheet->getStyle('A1')->getAlignment()->setWrapText(true);

            $panjang_tabel = range('B', 'Z');
            $sheet->SetCellValue("A2", "No");
            $sheet->getColumnDimension("A")->setWidth(6);
            $objPHPExcel->getActiveSheet()->getStyle("A2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');

            // Example for audiences
            $sheet->SetCellValue("A3", 1);

            // get field dan meta field pada tabel ini
            $example = [ 'user@example.com', 'password', 'User', 'Aktif', 'John Doe' ];

            $i = 0;
            $q = $this->M_permission->get_field_data(null);
            foreach($q as $key => $row){
                if(! in_array($row->name, $excluded)){
                    $sheet->SetCellValue("{$panjang_tabel[$i]}2", strtoupper(humanize($row->name)));
                    
                    // Example for audiences
                    //$sheet->SetCellValue("{$panjang_tabel[$i]}3", $example[$i]);
                    
                    $objPHPExcel->getActiveSheet()->getColumnDimension($panjang_tabel[$i])->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getStyle("{$panjang_tabel[$i]}2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');
                    $i++;
                }
            }

            // border untuk tabel data
            $akhir = ($i-1);
            $objPHPExcel->getActiveSheet()->getStyle("A2:{$panjang_tabel[$akhir]}20")->applyFromArray($styleArray);                        
            
            ////////////////////// TABLE REFERENSI //////////////////////
            // get data role sebagai referensi
            $i++;
            $awal = "{$panjang_tabel[$i]}2";            
            $sheet->SetCellValue("{$panjang_tabel[$i]}2", "Referensi APP ROLES ID");
            $objPHPExcel->getActiveSheet()->getColumnDimension($panjang_tabel[$i])->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle("{$panjang_tabel[$i]}2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');            

            $no = 3;
            $r  = $this->M_role->get(null, []);
            foreach($r as $row){
                $sheet->SetCellValue("{$panjang_tabel[$i]}{$no}", $row->name);
                $no++;
            }

            // border
            $akhir = ($no-1);
            $objPHPExcel->getActiveSheet()->getStyle("{$awal}:{$panjang_tabel[$i]}{$akhir}")->applyFromArray($styleArray);
            ////////////////////// END TABLE REFERENSI //////////////////////            

            // keterangan
            $akhir = ($no + 1);
            $sheet->SetCellValue("{$panjang_tabel[$i]}{$akhir}", "*) Hapus data Contoh!");
            
            $akhir++;
            $sheet->SetCellValue("{$panjang_tabel[$i]}{$akhir}", "**) APP ACCESS ID lihat sheet");

            // marge cell haeder
            $sheet->getStyle("A1:{$panjang_tabel[$i]}2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A1:{$panjang_tabel[$i]}2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);       
            $sheet->getStyle("A1:{$panjang_tabel[$i]}2")->getFont()->setBold(true);
            $sheet->mergeCells("A1:{$panjang_tabel[$i]}1");
            
            $objPHPExcel->getActiveSheet()->getStyle("A1:{$panjang_tabel[$i]}1")->applyFromArray($styleArray);

            // sheet 2 untuk daftar app_access_id
            $sheet2 = $objPHPExcel->createSheet(1);
            $sheet2->setTitle("REF APP ACCESS ID");
            $sheet2->SetCellValue('A1', "REFERENSI APP ACCESS ID ".strtoupper($this->module)." - ".strtoupper(app()->name)."\n".strtoupper(app()->company));
            $sheet2->getStyle('A1')->getAlignment()->setWrapText(true);
            
            // header tabel untuk access
            $panjang_tabel = range('A', 'Z');
            
            $i = 0;
            $q = $this->M_permission->get_field_data('app_access');
            foreach($q as $row){
                if($row->name == 'app_modules_id'){
                    $nama_header = 'MODULE NAME';
                }else{
                    $nama_header = $row->name;
                }

                $sheet2->SetCellValue("{$panjang_tabel[$i]}2", strtoupper(humanize($nama_header)));
                $objPHPExcel->getActiveSheet()->getColumnDimension($panjang_tabel[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getStyle("{$panjang_tabel[$i]}2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');
                $i++;
            }

            // value access
            $no = 3;
            $q  = $this->M_permission->get('app_access', []);
            foreach($q as $row){
                // cari nama asli modul
                $modul      = $this->M_module->get(null, [ 'id' => $row->app_modules_id ]);
                $nama_modul = (! empty($modul) && isset($modul[0]->name) ) ? $modul[0]->name : 'N/A';

                $sheet2->SetCellValue("A{$no}", $row->id);
                $sheet2->SetCellValue("B{$no}", $nama_modul);
                $sheet2->SetCellValue("C{$no}", ucwords($row->class_name));
                $sheet2->SetCellValue("D{$no}", $row->access_name);
                $no++;
            }

            // border
            $no--;
            $objPHPExcel->getActiveSheet()->getStyle("A1:D{$no}")->applyFromArray($styleArray);            

            // marge cell header sheet 2
            $sheet2->getStyle("A1:{$panjang_tabel[$i--]}2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle("A1:{$panjang_tabel[$i]}2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);       
            $sheet2->getStyle("A1:{$panjang_tabel[$i]}2")->getFont()->setBold(true);
            $sheet2->mergeCells("A1:{$panjang_tabel[$i]}1");

            // Set sheet yang aktif
            $objPHPExcel->setActiveSheetIndex(0);
                    
            // Set file properties
            $filename 	= "FORMAT IMPORT ".strtoupper($this->module)." - ".strtoupper(app()->name).".xls";
            $objPHPExcel->getProperties()->setTitle($filename);
            $objPHPExcel->getProperties()->setCreator("[DW] ".ucwords(app()->name).' by '.ucwords(app()->company));
            $objPHPExcel->getProperties()->setDescription("This file intended to use on ".ucwords(app()->name).' ecosystems only.');
            $objWriter 	= new PHPExcel_Writer_Excel5($objPHPExcel);
            
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');	
            
            $objWriter->save('php://output');
            exit();
        }
    }

    public function import_preview($id_en){
        
        $fail   = false;
        $id     = decrypt($id_en);
        $path   = './uploads/import/'.strtolower($this->module).'/'.$id.'.xls';

        if(! post('submit')){
            // cek apakah file ada diserver atau tidak
            if(! empty($id_en) && is_file($path)){
                
                $error      = 0;
                $range      = 'A';
                $field      = [];
                $valid_id   = [];

                // dapatkan field dari tabel ini
                $q = $this->M_permission->get_field_data(null);
                foreach($q as $row){
                    if(! in_array($row->name, [ 'id' ])){
                        array_push($field, $row->name);
                        $range++;
                    }
                }

                // karena data_row_status tidak masuk dalam list field tabel
                // dan kolom ini digunakan untuk menandai row bermasalah atau tidak
                array_push($field, 'data_row_status');
                
                // untuk kolom head nomor
                $data_head = [
                    ['data' => 'No', 'class' => 'text-center', 'style' => 'width:8%;']                    
                ];

                // temporary read data dari excelnya
                $a = excel_reader($path, $range, 3, [ 'app_access_id', 'app_roles_id' ], [ 'no' ]);
                foreach($a as $key => $row){

                    $row_invalid = false;

                    $data_row    = [
                        ['data' => ++$key, 'class' => 'text-center'],
                    ];

                    // dapatkan list field dari table yang dingunakan untuk import data
                    foreach($field as $row_field){
                        // table heading untuk pertama saja
                        if($key == 1){
                            if($row_field != 'data_row_status'){
                                array_push($data_head, ['data' => ucwords(humanize($row_field)), 'class' => 'text-center']);
                            }
                        }
                        
                        // cek apakah ini kolom untuk remarks atau keterangan?
                        if($row_field == 'data_row_status'){
                            if($row->$row_field == 1 && ! $row_invalid){
                                $valid = '<label class="label label-success">VALID</label>';

                                // masukkan key yang valid
                                array_push($valid_id, ($key - 1));
                            }else{
                                $valid = '<label class="label label-danger">INVALID</label>';
                                $error++;
                            }
                            array_push($data_row, ['data' => $valid, 'class' => 'text-center']);
                        }else{
                            // bila error, set background jadi merah
                            if($row->$row_field == '#__ERROR__#'){
                                array_push($data_row, ['data' => 'ERROR!', 'class' => 'text-center bg-red']);
                                $row_invalid = true;
                            }elseif($row_field == 'app_access_id' && ! $this->M_permission->get('app_access', [ 'id' => $row->$row_field ])){
                                array_push($data_row, ['data' => $row->$row_field, 'class' => 'text-center bg-red']);
                                $row_invalid = true;
                            }elseif($row_field == 'app_roles_id' && ! $this->M_role->get(null, [ 'name' => $row->$row_field ])){
                                array_push($data_row, ['data' => $row->$row_field, 'class' => 'text-center bg-red']);
                                $row_invalid = true;
                            }elseif($row_field != 'app_roles_id' && $row_field != 'app_access_id'){
                                // custom value, bila null atau 0 maka tampilkan tanda silang
                                // bila 1 maka tampilkan centang
                                if(! empty($row->$row_field)){
                                    $val = '<label class="label label-success"><i class="fa fa-check"></i></label>';
                                }else{
                                    $val = '<label class="label label-default"><i class="fa fa-minus"></i></label>';
                                }

                                array_push($data_row, ['data' => $val, 'class' => 'text-center']);
                            }else{
                                array_push($data_row, ['data' => $row->$row_field, 'class' => 'text-center']);                                
                            }
                        }
                    }

                    // masukkan dalam row baru untuk tabel di view
                    $this->table->add_row($data_row);
                }

                // finishing header tabel
                array_push($data_head, ['data' => 'Remark', 'class' => 'text-center', 'style' => 'width:14%;']);                        
                $this->table->set_heading($data_head);
                
                // data session untuk modul ini
                $data_sess_mod = [
                    'valid_id'  => $valid_id,
                    'max_range' => $range,
                    'fields'    => $field
                ];

                // masukkan dalam session valid id
                flash(['mod_'.$this->module => $data_sess_mod ]);

                $data['rows']   = count($a);
                $data['error']  = $error;
                $data['id']     = $id_en;
                $data['body']   = generate_table();
                $data['title']  = "Preview Import {$this->module}";
                $this->slice->view('commons.import_preview', $data);
            }
        }else{
            // cek apakah file ada diserver atau tidak
            if(! empty($id_en) && is_file($path)){
                $jumlah_insert  = 0;
                $data_sess_mod  = flash('mod_'.$this->module);
                $excel          = excel_reader($path, $data_sess_mod['max_range'], 3, [ 'app_roles_id', 'app_access_id' ], [ 'no' ]);
                
                // bandingkan data excel dengan id valid saja
                foreach($excel as $key => $row){
                    $is_valid = true;

                    if(in_array($key, $data_sess_mod['valid_id'])){
                        $data   = [];

                        // param ini untuk mencari apakah permission sudah ada atau belum, bila belum
                        // maka insert, bila sudah ada, maka update
                        $param  = [];

                        foreach($data_sess_mod['fields'] as $row_field){

                            if($row_field != 'data_row_status'){
                                switch($row_field){
                                    case "app_access_id":
                                        $a = $this->M_permission->get('app_access', [ 'id' => $row->$row_field ]);
                                        if($a){
                                            $data       += [ $row_field => $a[0]->id ];
                                            $param      += [ $row_field => $a[0]->id ];
                                        }else{
                                            $is_valid = false;
                                        }
                                        break;
                                    case "app_roles_id":
                                        $r = $this->M_role->get(null, [ 'name' => $row->$row_field ]);
                                        if($r){
                                            $data       += [ $row_field => $r[0]->id ];
                                            $param      += [ $row_field => $r[0]->id ];
                                        }else{
                                            $is_valid = false;
                                        }
                                        break;
                                    default:
                                        if(empty($row->$row_field)){
                                            $val = 0;
                                        }else{
                                            $val = 1;
                                        }
                                        $data       += [ $row_field => $val ];
                                        break;
                                }
                            }
                        }

                        // insert bila valid
                        if($is_valid){
                            if($q = $this->M_permission->get(null, $param)){
                                $i = $this->M_permission->update(null, [ 'id' => $q[0]->id ],$data);
                                if($i){
                                    $jumlah_insert++;
                                }
                            }else{
                                $i = $this->M_permission->insert(null, $data);
                                if($i){
                                    $jumlah_insert++;
                                }
                            }
                        }

                    }
                }

                // delete file diserver
                unlink($path);

                if($jumlah_insert > 0){
                    // success message
                    flash(['GLOBAL_ALERT_SUCCESS' => $jumlah_insert.'/'.count($data_sess_mod['valid_id']).' Data Berhasil Disimpan.']);
                    redirect(str_replace('/import_preview/'.$id_en, '', base_url(uri_string())));
                }else{
                    // fail message 
                    flash(['GLOBAL_ALERT_FAIL' => 'Data Gagal Disimpan. Silakan coba beberapa saat lagi.']);
                    redirect(str_replace('/import_preview/'.$id_en, '/import', base_url(uri_string())));
                }
            }
        }
    }

    public function delete_import(){
        // masukkan id yang tidak ingin dihapus
        $success    = false;
        if($this->request_method_delete && ! empty($id = $this->request_data['id'])){
            // bila banyak data maka hapus satu2
            $path   = './uploads/import/'.strtolower($this->module).'/'.$id.'.xls';

            if(is_file($path)){
                // delete file diserver
                unlink($path);
            }
        }

        echo $output;
    }

    function _result_table(){
        $this->table->set_heading(
            ['data' => 'No', 'class' => 'text-center', 'style' => 'width:8%;'],
            ['data' => 'Nama Role'],         
            ['data' => 'Akses / Permission', 'class' => 'text-center'],            
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

            // jumlah permission
            $permission = $this->M_permission->get_count(null, [ 'app_roles_id' => $row->id ]);

            $this->table->add_row(
                ['data' => ++$key, 'class' => 'text-center'],
                ['data' => "<b>{$row->name}</b><span class='clearfix'>Alias: {$row->alias}</span>"],           
                ['data' => $permission, 'class' => 'text-center'],
                ['data' => $action, 'class' => 'text-center']
            );
        }

        return generate_table();
    }

    function _filter($data = null){
        $filter = null;
        
        // modul dropdown
        $data2  = [ '' => ' -- SEMUA MODUL -- '];
        $module = $this->M_module->get(null, []);
        foreach($module as $row){
            $data2 += [ encrypt($row->id) => $row->name ];
        }
        
        $filter = filter_form([
            'Module'  => form_dropdown('module', $data2, get('module'), 'class="form-control selectpicker" data-live-search="true"'),
        ]);

        return $filter;
    }
}