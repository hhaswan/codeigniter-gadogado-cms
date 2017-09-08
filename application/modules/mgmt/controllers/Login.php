<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Front_Controller {
        
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        if(session($this->admin_identifier)){
            // session login sudah ada, redirect ke management
            redirect(base_url('mgmt'));
        }else{
            if(post('submit')){

                // request post, validasi form level backend
                $form_validate = validation([
                    ['email', 'Email', 'required'],
                    ['password', 'Password', 'required']
                ]);
                
                // cek return validasinya
                if($form_validate){
                    if(! empty($auth = $this->M_session->authenticate(post()))){
                        $data = [
                            'user_id'   => $auth[0]->id,
                            'email'     => $auth[0]->email,
                            'role_id'   => $auth[0]->app_role_id,
                            'full_name' => $auth[0]->full_name             
                        ];
                        session([$this->admin_identifier => $data]);
                        redirect(base_url('mgmt'));
                    }else{
                        flash(['MSG_ERROR' => "Email dan Password tidak dapat ditemukan."]);
                        $data['title'] = "Login";
                        $this->slice->view('login', $data);
                    }
                }else{
                    flash(['MSG_ERROR' => validation_error()]);
                    $data['title'] = "Login";
                    $this->slice->view('login', $data);    
                }
            }else{
                // belum login, tampilkan halaman login
                $data['title'] = "Login";
                $this->slice->view('login', $data);
            }
        }
    }

    public function logout(){
        // unset session yang valid saja, bila belum login, arahkan kembali ke halaman utama
        if(session($this->admin_identifier)){
            $this->session->unset_userdata($this->admin_identifier);
        }

        redirect(base_url(), 'refresh');
    }
}
