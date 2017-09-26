<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registration extends Admin_Controller {

    public function __construct(){
        if(app()->public_register){
            $opt = [
                'session_exception' => ['index', 'confirm']
            ];
        }else{
            $opt = [];
        }

        parent::__construct($opt);
    }
    
    public function index(){

        if(post('submit')){
            // submit form
            $form_validate = validation([
                ['nama', 'Nama Lengkap', 'trim|required'],
                ['email', 'Email', 'trim|required'],
                ['password', 'Password', 'required|xss_clean'],
                ['password_confirmation', 'Konfirmasi Password', 'required|xss_clean|matches[password]'],
                ['tos', 'Syarat dan Ketentuan', 'required|greater_than[0]']
            ]);
            
            // email sudah digunakan
            if($this->M_registration->get('app_users', [ 'email' => post('email') ])){
                flash(['MSG_ERROR' => "Email ini sudah digunakan."]);
                redirect(back());
            }

            if($form_validate){
                // apakah user baru harus konfirmasi email?
                // bila settingan mengharuskan maka pending statusnya dan kirim email ke user
                if(app()->register_validate){
                    $must_validate = 0;
                }else{
                    $must_validate = 1;
                }

                $q = $this->M_registration->create(post(), 2, $must_validate);
                if(! empty($q)){

                    // kirim email konfirmasi ke user baru
                    if(app()->register_validate){
                        if(! empty($token = $this->M_session->forget($q, 'P'))){

                            $msg = "Confirm User Here: ".base_url('/confirm/'.$token);
                            send_email("Confirm Email", $msg, null, post('email'));

                        }
                    }
                    
                    // success message 
                    $data['title'] = "Pendaftaran Berhasil";
                    $this->slice->view('registration.message', $data);
                }else{
                    // fail message 
                    flash(['GLOBAL_ALERT_FAIL' => 'Akun Gagal Dibuat, Silakan Ulangi Lagi.']);
                    redirect(back());
                }
            }else{
                flash(['MSG_ERROR' => validation_errors()]);
                redirect(back());
            }
        }else{
            // halaman register, bila belum login maka tampilkan
            if(! session($this->admin_identifier)){
                $data['title'] = "Pendaftaran";
                $this->slice->view('registration.public', $data);
            }else{
                // untuk user dengan auth arahkan ke modul management user
                redirect(base_url('mgmt/user'), 'refresh');
            }
        }

    }

    public function confirm($token){

        if(empty($token)){
            show_404();
        }else{
            // cek token untuk konfirmasi email
            if($result = $this->M_session->token($token, 'P')){
                // update status
                $i = $this->M_session->update('app_users', ['id' => $result[0]->app_users_id], [ 'status' => 1 ]);
                if($i){
                    // delete used token
                    if($this->M_session->delete('app_confirmations', [ 'token' => $token ])){
                        // success message 
                        flash(['GLOBAL_ALERT_SUCCESS' => 'Email Anda Berhasil Terkonfirmasi.']);
                    }else{
                        // failed message
                        flash(['GLOBAL_ALERT_FAIL' => 'Gagal Mengkonfirmasi Email Anda.']);
                    }
                }else{
                    // failed message
                    flash(['GLOBAL_ALERT_FAIL' => 'Gagal Mengkonfirmasi Email Anda.']);
                }

                $link = (app()->secure_login) ? '/'.app()->login_identifier : NULL;
                redirect(base_url('login'.$link));
            }else{
                show_404();
            }
        }
        
    }
}

