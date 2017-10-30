<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// defaultnya adalah mgmt, bila tidak ada session, lempar ke home
$route['default_controller']    = 'landing';
$route['404_override']          = '';
$route['translate_uri_dashes']  = FALSE;

// ini routes unik sesuai aplikasi
$route['dashboard']				= 'mgmt/mgmt/index';
$route['login']                 = 'mgmt/login/index';
$route['login/(:any)']          = 'mgmt/login/index/$1';
$route['register']              = 'mgmt/registration/index';
$route['confirm/(:any)']        = 'mgmt/registration/confirm/$1';
$route['forgot']                = 'mgmt/login/forgot';
$route['forgot/(:any)']         = 'mgmt/login/forgot/$1';
$route['forgot/(:any)/(:any)']  = 'mgmt/login/forgot/$1/$2';
$route['logout']                = 'mgmt/login/logout';
$route['profile']               = 'mgmt/profile/index';
$route['profile/(:any)']        = 'mgmt/profile/index/$1';
