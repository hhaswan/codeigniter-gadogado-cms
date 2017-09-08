<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// defaultnya adalah mgmt, bila tidak ada session, lempar ke home
$route['default_controller'] = 'landing';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// this is application routes
$route['login'] = 'mgmt/login';
$route['logout'] = 'mgmt/login/logout';
