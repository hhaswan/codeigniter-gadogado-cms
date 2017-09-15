<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;

class Role extends Admin_Controller {
        
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        $filter = null;

        // filter
        $data = [
            'type'      => 'text',
            'name'      => 'query',
            'class'     => 'form-control'            
        ];

        $data2 = [
            'opt1'      => 'AAAA',
            'opt2'      => 'AAAA',
            'opt3'      => 'AAAA'
        ];
        
        $filter = filter_form([
            'Dropdown'  => form_dropdown('dropdown', $data2, null, 'class="form-control selectpicker" data-live-search="true"'),
            'Keyword'   => form_input($data, null, 'placeholder="Kata Kunci"')
        ]);

        $data['filter'] = $filter;
        $data['title']  = "Management Role";
        $this->slice->view('role.index', $data);
    }
}
