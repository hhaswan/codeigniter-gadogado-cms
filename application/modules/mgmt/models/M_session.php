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
}