<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['app'] = [
    'name'              => "Nama Aplikasi",
    'tagline'           => "Tagline Aplikasi",
    'company'           => "Nama Organisasi",
    'timezone'          => "Asia/Makassar",
    'secure_login'      => false,
    'login_identifier'  => "secret",
    'register_validate' => true,
    'public_register'   => true,
    'landing_page'      => true,
    'version'			=> "1.0.0",
    'template_front'    => base_url('/landing/'),
    'template_back'     => base_url('/adminlte/'),
    'color_front'       => "#33414e",
    'color_back'        => "#33414e"
];
