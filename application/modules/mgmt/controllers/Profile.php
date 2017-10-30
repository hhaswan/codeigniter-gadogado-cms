<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

class Profile extends Admin_Controller {
        
    public function __construct(){
        parent::__construct();
    }
    
    public function index($user_idx = null){

        // submit dan non submit
        if(! post('submit') && ! post('submit_password')){
            // bila user id kosong maka tampilkan datanya saja        
            $user_id = (isset($user_idx)) ? decrypt($user_idx) : session($this->admin_identifier)['user_id'];
            
            $result  = $this->M_session->get('app_users', [ 'id' => $user_id]);
            if($result){
                $data['bio']    = $result[0]->bio;
                $data['reg']    = Carbon::parse($result[0]->created_at)->toFormattedDateString();
                $data['email']  = $result[0]->email;
                $data['name']   = $result[0]->full_name;
                $data['status'] = $result[0]->status;
                $data['role']   = $this->M_role->get(null, ['id' => $result[0]->app_role_id]);

                // dapatkan nama divisi dari user
                if($divisi = $this->M_division->get_user_division(['app_users_id' => $user_id])){
                    $n_d = $this->M_division->get(null,['id' => $divisi[0]->app_divisions_id]);
                    if($n_d){
                        $data['divisi']     = $this->user_data->division_name; 
                    }
                }

                // bila ini profile user sendiri
                if($user_id == session($this->admin_identifier)['user_id']){
                    $data['is_self']    = true;
                    $data['title']      = "Profil Saya"; 
                    $data['sessions']   = $this->M_session->get('app_sessions', [ 'app_users_id' => $user_id ], null, ['created_at' => 'desc']);
                }else{
                    $data['is_self']    = false;       
                    $data['title']      = "Profil {$result[0]->full_name}";
                }

                $this->slice->view('profile.index', $data);
            }else{
                // TODO: GANTI 404 PAGE
                show_404();
            }
        }elseif(post('submit')){
            
            // reject bila bukan diri sendiri
            if(! empty($user_id) && $user_id != session($this->admin_identifier)['user_id']){
                return false;
            }

            $form_validate = validation([
                ['nama', 'Nama Lengkap', 'trim|required'],
                ['email', 'Email', 'trim|required'],
                ['bio', 'Deskripsi (Bio)', 'trim|xss_clean']
            ]);

            if($form_validate){
                $data = [];
                $sess = session($this->admin_identifier);

                if(post('email') != session($this->admin_identifier)['email']){
                    // email ganti, kirim ke email baru untuk konfirmasi
                    $data += [ 'email' => post('email') ];
                    $sess['email'] = post('email');
                }

                // data baru untuk diupdate
                $data += [ 
                    'full_name' => post('nama'),
                    'bio'       => post('bio')
                ];

                // data session baru
                $sess['full_name'] = post('nama');

                $i = $this->M_session->update('app_users', [ 'id' => session($this->admin_identifier)['user_id'] ], $data);
                if($i){
                    // update session
                    session([ $this->admin_identifier => $sess ]);

                    // kirim email konfirmasi ke user baru
                    if(app()->register_validate){
                        if(! empty($token = $this->M_session->forget(session($this->admin_identifier)['user_id'], 'P'))){
                            $msg = "We notice that you've changed your Email. Please Confirm User Here: ".base_url('/confirm/'.$token);
                            send_email("Confirm Email", $msg, null, post('email'));
                        }
                    }
                    
                    // success message 
                    flash(['GLOBAL_ALERT_SUCCESS' => 'Data Berhasil Disimpan.']);
                }else{
                    // fail message 
                    flash(['GLOBAL_ALERT_FAIL' => 'Data Gagal Disimpan, Silakan Ulangi Lagi.']);
                }

                // kembali ke halaman sebelumnya
                redirect(back());
            }else{
                flash(['MSG_ERROR' => validation_errors()]);
                redirect(back());
            }
        }elseif(post('submit_password')){
            
            // reject bila bukan diri sendiri
            if(! empty($user_id) && $user_id != session($this->admin_identifier)['user_id']){
                return false;
            }

            // validate form
            $form_validate = validation([
                ['password_current', 'Password Saat Ini', 'required'],                
                ['password', 'Password', 'required'],
                ['password_confirmation', 'Konfirmasi Password', 'required|matches[password]'],
            ]);

            if($form_validate){
                if($this->M_session->authenticate([ 'email' => session($this->admin_identifier)['email'], 'password' => post('password_current') ])){
                    $salt = random_string('alnum', 128);
                    $data = [ 
                        'salt'      => $salt,
                        'password'  => hash("sha512", post('password').$salt, FALSE)
                    ];
                    $i = $this->M_session->update('app_users', [ 'id' => session($this->admin_identifier)['user_id'] ], $data);
                    if($i){
                        flash(['GLOBAL_ALERT_SUCCESS' => 'Password Berhasil Diganti.']);
                    }else{
                        flash(['GLOBAL_ALERT_FAIL' => 'Password Gagal Diganti, Silakan Ulangi Lagi.']);                
                    }
                }else{
                    flash(['GLOBAL_ALERT_FAIL' => 'Password Lama Anda Tidak Sesuai, Silakan Ulangi Lagi.']);
                }

                // kembali ke halaman sebelumnya
                redirect(back());
            }else{
                flash(['MSG_ERROR' => validation_errors()]);
                redirect(back());
            }
        }

    }

    public function ajax_terminate_session(){

        // predefault ajax output
        $output = [ 'status' => false ];
        $token  = $this->M_session->get('app_sessions', [ 'token' => session($this->admin_identifier)['token'] ]);
        
        if($this->request_method_delete && $token){

            // bila token ini milik user sendiri
            if($token[0]->app_users_id == session($this->admin_identifier)['user_id']){
                // hapus sessionnya
                $d = $this->M_session->delete('app_sessions', [ 'token' => $this->request_data['token'] ]);
                if($d){
                    $data['sessions']   = $this->M_session->get('app_sessions', [ 'app_users_id' => session($this->admin_identifier)['user_id'] ]);
                    $view   = $this->slice->view('profile.security', $data, TRUE);
                    $output = [ 'status' => true, 'html' => $view ];
                }
            }

        }

        echo json_encode($output);
    }
}