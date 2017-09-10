<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// defaultnya adalah mgmt, bila tidak ada session, lempar ke home
$route['default_controller']    = 'landing';
$route['404_override']          = '';
$route['translate_uri_dashes']  = FALSE;

// this is application routes
$route['login']                 = 'mgmt/login/index';
$route['login/(:any)']          = 'mgmt/login/index/$1';
$route['forgot']                = 'mgmt/login/forgot';
$route['forgot/(:any)']         = 'mgmt/login/forgot/$1';
$route['forgot/(:any)/(:any)']  = 'mgmt/login/forgot/$1/$2';
$route['logout']                = 'mgmt/login/logout';
