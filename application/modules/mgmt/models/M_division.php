<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_division extends MY_Model{

    function __construct(){
        $this->table = 'app_divisions';
    }

    // User Divisions
    public function get_user_division(array $param = []){
    	return $this->get('app_user_divisions', $param);
    }

    public function insert_user_division(array $data = []){
    	return $this->insert('app_user_divisions', $data);
    }

    public function update_user_division(array $param = [], array $data = []){
    	return $this->update('app_user_divisions', $param, $data);
    }

    public function delete_user_division(array $param = []){
    	return $this->delete('app_user_divisions', $param);
    }

    // Division Access
    public function get_access_division(array $param = []){
        return $this->get('app_division_access', $param);
    }

    public function insert_access_division(array $data = []){
        return $this->insert('app_division_access', $data);
    }

    public function update_access_division(array $param = [], array $data = []){
        return $this->update('app_division_access', $param, $data);
    }

    public function delete_access_division(array $param = []){
        return $this->delete('app_division_access', $param);
    }

}