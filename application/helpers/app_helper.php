<?php defined('BASEPATH') OR exit('No direct script access allowed');

 /**
* App Propertis
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