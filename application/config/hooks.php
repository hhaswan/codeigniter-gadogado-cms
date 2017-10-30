<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$hook['pre_system'] = function() {
    $dotenv = new Dotenv\Dotenv(APPPATH);
    $dotenv->load();
};

/*
* TODO: Activate this
$hook['post_controller'][] = array(
    'class'    => 'NullPageCollector',
    'function' => 'is_nulled',
    'filename' => 'NullPageCollector.php',
    'filepath' => 'hooks'
);*/

$hook['post_controller'][] = array(
    'class'    => 'PageSecurity',
    'function' => 'rerender_output',
    'filename' => 'PageSecurity.php',
    'filepath' => 'hooks'
);