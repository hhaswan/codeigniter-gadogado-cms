<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_registration extends MY_Model{
    
    protected $table = 'app_users';    

    public function create($request, $role_id, $status = 0, $division){
        
        $output = false;

        // cek email dan password dari request
        if(key_exists("email", $request) && key_exists("password", $request)){

            // salt password
            $salt   = random_string('alnum', 128);
            $query  = $this->insert($this->table, [
                'email'         => $request['email'],
                'salt'          => $salt,
                'password'      => hash("sha512", $request['password'].$salt, FALSE),
                'app_role_id'   => $role_id,
                'status'        => $status,
                'full_name'     => $request['nama'],
                'created_at'    => \Carbon\Carbon::now()
            ]);
            
            // return sesuai hasil query
            if(empty($query) || $query == 0){
                // insert divi peruser
                $this->insert('app_user_divisions', ['app_users_id' => $query, 'app_divisions_id' => $division]);
                
                $output = true;
            }else{
                $output = $query;                
            }
        }

        return $output;
    }
}