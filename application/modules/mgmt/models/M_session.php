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

    public function forget($user_id, $type = 'F'){

        // generate token
        $token  = random_string('alnum', 64);
        if(! $entry = $this->get('app_confirmations', ['app_users_id' => $user_id, 'type' => $type])){
            $i      = $this->insert('app_confirmations', [
                'token'         => $token,
                'app_users_id'  => $user_id,
                'type'          => $type
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

    public function create($token, $user_id){
        
        $output = false;
        
        if(! empty($token) && ! empty($user_id)){
            // salt password
            $query  = $this->insert('app_sessions', [
                'token'         => $token,
                'created_at'    => \Carbon\Carbon::now(),
                'user_agent'    => $this->agent->agent_string(),
                'ip_address'    => $this->input->ip_address(),
                'app_users_id'  => $user_id
            ]);
            
            // return sesuai hasil query
            if(empty($query) || $query == 0){
                $output = true;
            }
        }            

        return $output;
    }

    public function token($token, $type){
        return $this->get('app_confirmations', ['token' => $token, 'type' => $type]);
    }
}