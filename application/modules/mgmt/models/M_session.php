<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_session extends MY_Model{

    protected $table = 'app_users';
    
    public function authenticate($request){
        
        $output = null;

        // cek email dan password
        if(key_exists("email", $request) && key_exists("password", $request)){
            $query = $this->get($this->table, [
                'email'     => $request['email']
            ]);
            
            // cek hashed password user
            if($query){
                $salt               = $query[0]->salt;
                $password_hashed    = hash("sha512", $request['password'].$salt, FALSE);
                
                if($query[0]->password == $password_hashed){
                    unset($query[0]->password);
                    unset($query[0]->salt);
                    $output = $query;
                }
            }
        }

        return $output;
    }

    function coba(){
        return $this->db->field_data('app_confirmations');
    }

    public function forget($user_id){

        // generate token
        $token  = random_string('alnum', 64);
        if(! $entry = $this->get('app_confirmations', ['app_users_id' => $user_id, 'type' => 'F'])){
            $i      = $this->insert('app_confirmations', [
                'token'         => $token,
                'app_users_id'  => $user_id,
                'type'          => 'F'
            ]);
        }else{
            $i = $this->update(
                'app_confirmations', 
                [ 'token' => $entry[0]->token],
                [ 'token' => $token]
            );
        }
        
        if($i){
            $output = $token;
        }else{
            $output = null;
        }

        return $output;
    }

    public function token($token, $type){
        return $this->get('app_confirmations', ['token' => $token, 'type' => $type]);
    }
}