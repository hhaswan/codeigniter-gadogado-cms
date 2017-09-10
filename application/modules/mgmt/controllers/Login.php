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
                        $this->slice->view('login.index', $data);
                    }
                }else{
                    flash(['MSG_ERROR' => validation_errors()]);
                    $data['title'] = "Login";
                    $this->slice->view('login.index', $data);    
                }
            }else{

                // apakah login ini secure apa tidak? (user spam untuk dapatkan login page)
                if($this->_is_secure_login()){
                    // belum login, tampilkan halaman login
                    $data['title'] = "Login";
                    $this->slice->view('login.index', $data);
                }else{
                    // login tidak dikenali, tampilkan halaman 404
                    show_404();
                }

            }
        }
    }

    public function forgot($token = null){

        if(post('submit')){
            // halaman post password baru
            $segment    = $this->uri->segment_array();
            $token      = end($segment);

            // cek token
            if(! empty($result = $this->M_session->token($token, 'F'))){
                // request post, validasi form level backend
                $form_validate = validation([
                    ['password', 'Password', 'required'],
                    ['password_confirmation', 'Konfirmasi Password', 'required|matches[password]'],
                ]);

                // cek return validasinya
                if($form_validate){

                    // generate salt & combine with inputed password
                    $salt       = random_string('alnum', 128);
                    $password   = hash("sha512", post('password').$salt, FALSE);

                    // reset passord data
                    $data = [
                        'password'  => $password,
                        'salt'      => $salt
                    ];

                    // update password
                    $i = $this->M_session->update('app_users', ['id' => $result[0]->app_users_id], $data);
                    if($i){
                        // delete used token
                        $this->M_session->delete('app_confirmations', [ 'token' => $token]);

                        // success message 
                        flash(['GLOBAL_ALERT_SUCCESS' => 'Password Berhasil Diganti.']);
                        redirect("login/".app()->login_identifier);
                    }else{
                        // failed message
                        flash(['GLOBAL_ALERT_SUCCESS' => 'Gagal Mengganti Password Anda.']);                        
                        redirect(back());
                    }
                }else{
                    flash(['MSG_ERROR' => validation_errors()]);
                    redirect(back());
                }
            }else{
                redirect(back());
            }
        }elseif($this->_is_secure_login()){
            if($token == app()->login_identifier){
                if(! post('submit_send')){
                    
                    // halaman forgot password
                    $data['title'] = "Lupa Password";
                    $this->slice->view('login.forgot', $data);
                }else{
                    // request post, validasi form level backend
                    $form_validate = validation([
                        ['email', 'Email', 'required']
                    ]);
                    
                    // cek apakah email ini terdaftar
                    $registered = $this->M_session->get('app_users', ['email' => post('email')]);

                    // cek return validasinya
                    if($form_validate && $registered){

                        // generate token untuk forgot password
                        if(! empty($token = $this->M_session->forget($registered[0]->id))){
                            // kirim email
                            $msg = "Reset Here: {$token}";
                            if(send_email("Reset Password", $msg, null, "anna@example.com")){
                                flash(['GLOBAL_ALERT_SUCCESS' => 'Periksa Email Anda untuk melanjutkan proses reset password.']);
                            }else{
                                flash(['GLOBAL_ALERT_SUCCESS' => 'Email gagal dikirim, cobalah beberapa saat lagi.']);
                            }

                            // redirect ke halaman sebelumnya.
                            redirect(back());
                        }
                    }else{
                        // pesan error
                        if($registered){
                            flash(['MSG_ERROR' => validation_errors()]);
                        }else{
                            flash(['MSG_ERROR' => "Maaf, Email ini tidak terdaftar."]);
                        }

                        $data['title'] = "Lupa Password";
                        $this->slice->view('login.forgot', $data);
                    }
                }
            }else{
                // halaman reset password
                $data['title'] = "Atur Ulang Password";
                $this->slice->view('login.reset', $data);
            }
        }else{
            show_404();
        }

    }

    public function logout(){
        // unset session yang valid saja, bila belum login, arahkan kembali ke halaman utama
        if(session($this->admin_identifier)){
            $this->session->unset_userdata($this->admin_identifier);
        }

        redirect(base_url(), 'refresh');
    }

    function _is_secure_login(){
        $output = true;

        if(app()->secure_login){
            $segment = $this->uri->segment_array();
            if(end($segment) != app()->login_identifier){

                // cari token di tabel app_confirmations
                if(! $this->M_session->get('app_confirmations', ['token' => end($segment), 'type' => 'F'])){
                    $output = false;
                }

            }
        }

        return $output;
    }
}

