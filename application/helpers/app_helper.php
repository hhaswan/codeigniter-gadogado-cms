<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* App Properties
*
* @return void
* @author Dimas Wicaksono
**/
if ( ! function_exists('app')){
    function app(){
        $CI =& get_instance();
        
        // get application's setting
        return (object) $CI->load->config('app');
    }
}

/**
* Filter Form
*
* @return string
* @author Dimas Wicaksono
**/
if ( ! function_exists('filter_form')){
    function filter_form(array $content = []){

        $output = null;

        $CI =& get_instance();
        
        if(! empty($content) && is_array($content)){
            foreach($content as $key => $val){
                $output .= '<div class="form-group">
                            <label class="col-sm-3 control-label">'.$key.'</label>
                            <div class="col-sm-9">
                                '.$val.'
                            </div>
                        </div>';
            }
        }

        return $output;
    }
}

/**
* Send Email
*
* @return boolean
* @author Dimas Wicaksono
**/
if ( ! function_exists('send_email')){
    function send_email($subject = null, $message, $sender, $respondent){
        
        $output = false;

        // get CI instance
        $CI =& get_instance();
        
        // load config for email library
        $config = Array(
            'protocol'  => getenv('MAIL_PROTOCOL'),
            'smtp_host' => getenv('MAIL_HOST'),
            'smtp_port' => getenv('MAIL_PORT'),
            'smtp_user' => getenv('MAIL_USER'),
            'smtp_pass' => getenv('MAIL_PASS'),
            'crlf' => "\r\n",
            'newline' => "\r\n"
        );
        $CI->load->library('email', $config);

        // email operation
        $CI->email->from((isset($sender)) ? $sender : getenv('MAIL_SENDER'));
        $CI->email->to($respondent);
        $CI->email->subject($subject);
        $CI->email->message($message);

        if($CI->email->send()){
            $output = true;
        }else{
            show_error($CI->email->print_debugger());            
        }

        return $output;
    }
}

/**
* Encrypt
*
* @return string
* @author Dimas Wicaksono
**/
if ( ! function_exists('encrypt')){
    function encrypt($string){
        $CI =& get_instance();
        
        $CI->encryption->initialize(array('cipher' => 'tripledes', 'mode' => 'cbc'));
        $ciphertext = $CI->encryption->encrypt($string);
        $str  		= str_replace(array('+', '/', '='), array('-', '_', '~'), $ciphertext);
        $str 		= base64_encode($str);
        $str  		= str_replace(array('+', '/', '='), array('-', '_', '~'), $str);		
        return $str;
    }
}

/**
* Decrypt
*
* @return string
* @author Dimas Wicaksono
**/
if ( ! function_exists('decrypt')){
    function decrypt($string){
        $CI =& get_instance();
		
		$CI->encryption->initialize(array('cipher' => 'tripledes','mode' => 'cbc'));
		$string 	= str_replace(array('-', '_', '~'), array('+', '/', '='), $string);
		$string 	= base64_decode($string);
		$str 		= str_replace(array('-', '_', '~'), array('+', '/', '='), $string);
		$str 		= $CI->encryption->decrypt($str);
		return $str;
    }
}

/**
* POST Request
*
* @return string
* @author Dimas Wicaksono
**/
if ( ! function_exists('post')){
    function post($name = null){
        $CI =& get_instance();
        
        // get application's setting
        return $CI->input->post($name);
    }
}

/**
* GET Request
*
* @return string
* @author Dimas Wicaksono
**/
if ( ! function_exists('get')){
    function get($name = null){
        $CI =& get_instance();
        
        // get application's setting
        return $CI->input->get($name);
    }
}

/**
* Validation Helper
*
* @return boolean
* @author Dimas Wicaksono
**/
if ( ! function_exists('validation')){
    function validation($options){
        $CI =& get_instance();
        
        // cek apakah options itu array atau bukan
        if(is_array($options)){
            // bila jumlah options ini lebih dari 1 maka looping array childnya
            if(count($options[0]) > 1){
                foreach($options as $row){
                    if(is_array($row)){
                        // cek apakah row itu array, bila array optionnya lebih dari satu
                        $name       = (isset($row[0])) ? $row[0] : die("ERROR_ITEM_NAME_NOT_FOUND");
                        $real_name  = (isset($row[1])) ? $row[1] : $name;
                        $rule       = (isset($row[2])) ? $row[2] : NULL;
                        $CI->form_validation->set_rules($name, $real_name, $rule);
                    }
                }
            }else{
                // bila bukan langsung masukkan dalam rule
                $name       = (isset($options[0])) ? $options[0] : die("ERROR_ITEM_NAME_NOT_FOUND");
                $real_name  = (isset($options[1])) ? $options[1] : $name;
                $rule       = (isset($options[2])) ? $options[2] : NULL;
                $CI->form_validation->set_rules($name, $real_name, $rule);
            }

            // running validation
            return $CI->form_validation->run();
        }
    }
}

/**
* Session
*
* @return mixed
* @author Dimas Wicaksono
**/
if ( ! function_exists('session')){
    function session($context = null, $is_temp = false){
        $CI =& get_instance();
        
        // bila array maka, masukkan konten array ke dalam session
        if(is_array($context)){
            foreach($context as $key => $row){
                // bila data temporary
                if($is_temp){
                    $CI->session->set_tempdata($key, $row, 600);
                }else{
                    $CI->session->set_userdata($key, $row);
                }
                $output = true;
            }
        }else{
            // get result from session
            if($is_temp){
                $output = $CI->session->tempdata($context);                
            }else{
                $output = $CI->session->userdata($context);
            }             
        }

        return $output;
    }
}

/**
* Flash Session
*
* @return mixed
* @author Dimas Wicaksono
**/
if ( ! function_exists('flash')){
    function flash($context = null){
        $CI =& get_instance();
        
        // bila array maka, masukkan konten array ke dalam session
        if(is_array($context)){
            foreach($context as $key => $row){
                $CI->session->set_flashdata($key, $row);
            }
            $output = true;            
        }else{
            // get result from session
            $output = $CI->session->flashdata($context);
        }

        return $output;
    }
}

/**
* Current Access
*
* @return void
* @author Dimas Wicaksono
**/
if ( ! function_exists('access')){
    function access(){
        $CI =& get_instance();

        $param_ori  = $CI->uri->segment_array();
		$param      =   str_replace(
                            array($CI->router->fetch_module(), $CI->router->fetch_class(), $CI->router->fetch_method()),
                            array(null,null,null),
                            end($param_ori) );
        
        // get application's setting
        return (object) [
            'controller'    => $CI->router->fetch_class(),
            'method'        => $CI->router->fetch_method(),
            'module'        => $CI->router->fetch_module(),
            'param'         => $param
        ];
    }
}

/**
* Back (History -1)
*
* @return void
* @author Dimas Wicaksono
**/
if ( ! function_exists('back')){
    function back(){
        $CI =& get_instance();

        return $CI->agent->referrer();
    }
}

/**
* Breadcrumb
*
* @return void
* @author Dimas Wicaksono
**/
if ( ! function_exists('breadcrumb')){
    function breadcrumb($default_controller = null){
        $array          = array();
        $similar_all 	= false;

        // instance CI
        $CI =& get_instance();        

        // bila empty maka lookup dari config router saja untuk default controllernya
        if(empty($default_controller)){
            $default_controller = $CI->router->default_controller;
            $array += [base_url() => "<i class='fa fa-home'></i> Beranda"];
        }else{
            $array += [base_url($default_controller) => "<i class='fa fa-home'></i> Beranda"];
        }

		if(in_array(access()->module, $CI->uri->segment_array())){
			if(in_array(access()->controller, $CI->uri->segment_array())){
				if(in_array(access()->method, $CI->uri->segment_array())){
					$similar_all = true;
				}		
			}	
        }

		if($similar_all){
			// Bila ada kesamaan, maka generate hanya modul/controller/method/last param utk breadcrumb nya
			// Bila tidak pake custom route, tampilkan urutan module/cont/method/param	
			if(access()->module!=$default_controller or access()->module!=access()->controller){
				$link = base_url(access()->module);
                $array += [$link => ucwords(humanize(access()->module,'-'))];
			}

            // bila controller tidak sama dengan deault_controller
			if(access()->controller!=$default_controller){
				$link = base_url(access()->controller);
                $array += [$link => ucwords(humanize(access()->controller,'-'))];
			}

            // bila module tidak sama dengan deault_controller            
			if(access()->module!=access()->controller){
				$link = base_url(access()->module.'/'.access()->controller.'/'.access()->method);
			}else{
				$link = base_url(access()->controller.'/'.access()->method);
			}
            
            if(! empty(access()->param)){
                $array += [$link => ucwords(humanize(access()->method,'-'))];
                $array += ['li-active' => ucwords(humanize(access()->param,'-'))];
            }else{
                // bila method yang paling terakhir, maka set active
                $array += ['li-active' => ucwords(humanize(access()->method,'-'))];
            }
		}else{
			// Custom route
			// bila string tidak ada di bagian module/controller/method, tampilkan semua (foreach)
			$link = null;
			$segment = $CI->uri->segment_array();
			foreach($segment as $item){
				if($item!=$default_controller){
					$link .= $item.'/';
				}	

				if($item!=$default_controller){
					if(end($segment) == $item){
                        $array += ['li-active' => ucwords(humanize($item,'-'))];        
					}else{
                        $array += [base_url($link) => ucwords(humanize($item,'-'))];                                
					}
				}
			}
		}
		return $array;
	}
}