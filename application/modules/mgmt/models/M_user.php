<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_user extends MY_Model{

    function __construct(){
        $this->table = 'app_users';
    }

}