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
            $excluded   = [ 'id', 'salt', 'created_at', 'otp', 'bio' ];

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

            // Set sheet yang aktif
            $objPHPExcel->setActiveSheetIndex(0);

            $panjang_tabel = range('B', 'Z');
            $sheet->SetCellValue("A2", "No");
            $sheet->getColumnDimension("A")->setWidth(6);
            $objPHPExcel->getActiveSheet()->getStyle("A2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');

            // Example for audiences
            $sheet->SetCellValue("A3", 1);

            // get field dan meta field pada tabel ini
            $example = [ 'user@example.com', 'password', 'User', 'Aktif', 'John Doe' ];

            $i = 0;
            $q = $this->M_user->get_field_data(null);
            foreach($q as $key => $row){
                if(! in_array($row->name, $excluded)){
                    $sheet->SetCellValue("{$panjang_tabel[$i]}2", strtoupper(humanize($row->name)));
                    
                    // Example for audiences
                    $sheet->SetCellValue("{$panjang_tabel[$i]}3", $example[$i]);
                    
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
            $sheet->SetCellValue("{$panjang_tabel[$i]}2", "Referensi APP ROLE ID");
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

            // status user
            $no++;
            $awal = $panjang_tabel[$i].$no;
            $sheet->SetCellValue("{$panjang_tabel[$i]}{$no}", "Referensi STATUS");
            $objPHPExcel->getActiveSheet()->getColumnDimension($panjang_tabel[$i])->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle("{$panjang_tabel[$i]}{$no}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DDDDDD');
            $sheet->getStyle("{$panjang_tabel[$i]}{$no}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$panjang_tabel[$i]}{$no}")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getStyle("{$panjang_tabel[$i]}{$no}")->getFont()->setBold(true);            

            $status = [ 'Non-Aktif', 'Aktif', 'Pending' ];
            foreach($status as $row){
                $no++;   
                $sheet->SetCellValue("{$panjang_tabel[$i]}{$no}", $row);
            }

            $akhir = ($no + 2);
            $sheet->SetCellValue("{$panjang_tabel[$i]}{$akhir}", "*) Hapus data Contoh!");
            
            // border
            $objPHPExcel->getActiveSheet()->getStyle("{$awal}:{$panjang_tabel[$i]}{$no}")->applyFromArray($styleArray);
            ////////////////////// END TABLE REFERENSI //////////////////////            

            // marge cell haeder
            $sheet->getStyle("A1:{$panjang_tabel[$i]}2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A1:{$panjang_tabel[$i]}2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);       
            $sheet->getStyle("A1:{$panjang_tabel[$i]}2")->getFont()->setBold(true);
            $sheet->mergeCells("A1:{$panjang_tabel[$i]}1");
            
            $objPHPExcel->getActiveSheet()->getStyle("A1:{$panjang_tabel[$i]}1")->applyFromArray($styleArray);
                    
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
                $q = $this->M_user->get_field_data(null);
                foreach($q as $row){
                    if(! in_array($row->name, [ 'id', 'salt', 'created_at', 'otp', 'bio' ])){
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
                $a = excel_reader($path, $range, 3, [ 'app_role_id', 'status', 'email' ], [ 'no' ]);
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
                            }elseif($row_field == 'email' && $this->M_user->get(null, [ 'email' => $row->$row_field ])){
                                array_push($data_row, ['data' => $row->$row_field, 'class' => 'text-center bg-red']);
                                $row_invalid = true;
                            }elseif($row_field == 'app_role_id' && ! $this->M_role->get(null, [ 'name' => $row->$row_field ])){
                                array_push($data_row, ['data' => $row->$row_field, 'class' => 'text-center bg-red']);
                                $row_invalid = true;
                            }elseif($row_field == 'status' && ! in_array($row->$row_field, [ 'Aktif', 'Non-Aktif', 'Pending' ])){
                                array_push($data_row, ['data' => $row->$row_field, 'class' => 'text-center bg-red']);
                                $row_invalid = true;
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
                $excel          = excel_reader($path, $data_sess_mod['max_range'], 3, [ 'app_role_id', 'status', 'email' ], [ 'no' ]);
                
                // bandingkan data excel dengan id valid saja
                foreach($excel as $key => $row){
                    $is_valid = true;

                    if(in_array($key, $data_sess_mod['valid_id'])){
                        $data = [ 'created_at' => Carbon::now() ];
                        foreach($data_sess_mod['fields'] as $row_field){
                            if($row_field == 'email'){
                                if($this->M_user->get(null, [ 'email' => $row->$row_field ])){
                                    $is_valid = false;
                                }
                            }
                            if($row_field != 'data_row_status'){
                                switch($row_field){
                                    case "password":
                                        $salt       = random_string('alnum', 128);
                                        $password   = hash("sha512", $row->$row_field.$salt, FALSE);
                                        $data       += [ $row_field => $password ];
                                        $data       += [ 'salt' => $salt ];
                                        break;
                                    case "app_role_id":
                                        $r = $this->M_role->get(null, [ 'name' => $row->$row_field ]);
                                        if($r){
                                            $data       += [ $row_field => $r[0]->id ];
                                        }else{
                                            $is_valid = false;
                                        }
                                        break;
                                    case "status":
                                        if($row->$row_field == 'Aktif'){
                                            $sts = 1;
                                        }elseif($row->$row_field == 'Non-Aktif'){
                                            $sts = 0;                                            
                                        }elseif($row->$row_field == 'Pending'){
                                            $sts = 2;                                            
                                        }else{
                                            $is_valid = false;
                                        }
                                        $data       += [ $row_field => $sts ];
                                        break;
                                    default:
                                        $data       += [ $row_field => $row->$row_field ];                                
                                        break;
                                }
                            }
                        }

                        // insert bila valid
                        if($is_valid){
                            $i = $this->M_user->insert(null, $data);
                            if($i){
                                $jumlah_insert++;
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