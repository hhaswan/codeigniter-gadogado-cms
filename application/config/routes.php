<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// defaultnya adalah mgmt, bila tidak ada session, lempar ke home
$route['default_controller'] = 'mgmt';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
