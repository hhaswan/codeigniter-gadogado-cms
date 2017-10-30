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
* Table Generator
*
* @return string
* @author Dimas Wicaksono
**/
if ( ! function_exists('generate_table')){
    function generate_table(){
        $CI =& get_instance();
        
        $template = array(
            'table_open'            => '<div class="table-responsive"><table class="table table-bordered table-hover datatable">',
            'thead_open'            => '<thead style="background:#EEE">',
            'thead_close'           => '</thead>',
            'heading_row_start'     => '<tr>',
            'heading_row_end'       => '</tr>',
            'heading_cell_start'    => '<th style="vertical-align:middle;">',
            'heading_cell_end'      => '</th>',
            'tbody_open'            => '<tbody>',
            'tbody_close'           => '</tbody>',
            'row_start'             => '<tr>',
            'row_end'               => '</tr>',
            'cell_start'            => '<td style="vertical-align:middle;">',
            'cell_end'              => '</td>',
            'row_alt_start'         => '<tr>',
            'row_alt_end'           => '</tr>',
            'cell_alt_start'        => '<td style="vertical-align:middle;">',
            'cell_alt_end'          => '</td>',    
            'table_close'           => '</table></div>'
        );
        $CI->table->set_template($template);
    
        return $CI->table->generate();
    }
}

/**
* Actions Button Generator
*
* @return string
* @author Dimas Wicaksono
**/
if ( ! function_exists('generate_actions')){
    function generate_actions(array $option = [], $permission = null, $class){
        $output     = [];
        $list       = null;
        $is_empty   = true;

        foreach($option as $key => $row){
            // cek permission
            if(! empty($permission)){
                if(isset($permission->$key) && $permission->$key){

                    // cek apakah dalam class ini (ctrl) ada method dengan key ini
                    if(method_exists($class, $key)){
                        
                        $is_empty = false;

                        if($key == 'delete'){
                            $list .= '<li class="divider"></li><li>'.$row.'</li>';
                        }else{
                            $list .= '<li>'.$row.'</li>';
                        }
                    }

                }     
            }else{    
                if(method_exists($class, $key)){
                    if($key == 'delete'){
                        $list .= '<li class="divider"></li><li>'.$row.'</li>';
                    }else{
                        $list .= '<li>'.$row.'</li>';
                    }
                }
            }
        }

        if(! $is_empty){
            $output = '<div class="btn-group"><button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-cog"></i>&nbsp;<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
            </button><ul class="dropdown-menu dropdown-menu-right" role="menu">'.$list.'</ul></div>';
        }else{
            $output = '<i class="fa fa-eye-slash text-gray fa-fw"></i>';
        }

        return $output;
    }
}

/**
* Excel Import Reader
*
* @return array
* @author Dimas Wicaksono
**/
if ( ! function_exists('excel_reader')){
    function excel_reader($path, $max_column, $start_row = 3, array $mandatory_list = [], array $ignore_list = []){

        $output = [];
        
        // baca excel file yang ada dalam server
        $excelReader    = PHPExcel_IOFactory::createReaderForFile($path);
        $excelObj       = $excelReader->load($path);
        $sheet          = $excelObj->getSheet(0);

        // kolom palng bawah yang kemungkinan ada data
        $highestRow     = $sheet->getHighestRow();
        $filledCol      = range('A', $max_column);

        for ($row = $start_row; $row <= $highestRow; $row++) { 
            
            // rerset data baris
            $data           = [];

            // cek row yang ada errornya
            $row_status     = 1;

            // dimulai dari 1 krn nomor tidak dianggap
            $empty_value    = count($ignore_list);

            // dapatkan data di tiap kolom sesuai dengan parameter
            foreach($filledCol as $row_col){
                $header = url_title(strtolower($sheet->getCell($row_col."2")->getValue()), 'underscore');
                $value  = $sheet->getCell($row_col.$row)->getValue();
                
                // bila tidak kosong, masukkan data
                if(! in_array($header, $ignore_list)){
                    if(! empty($value)){
                        $data += [ $header => $value ];
                    }elseif(empty($value) && in_array($header, $mandatory_list)){
                        // kosong dan termasuk mandatory
                        $data += [ $header => '#__ERROR__#' ];
                        $empty_value++;
                        $row_status = 0;
                    }else{
                        // kosong tidak termasuk mandatory
                        $data += [ $header => null ];
                        $empty_value++;
                    }
                }

                // bila row kosong semua (berarti tidak ada data yang diinput di row ini)
                if($empty_value >= count($filledCol)){
                    $data = [];
                }
            }
            if(! empty($data)){
                $data += [ 'data_row_status' => $row_status ];
                array_push($output, (object) $data);
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
* Complex Encrypt
*
* @return string
* @author Dimas Wicaksono
**/
if ( ! function_exists('complex_encrypt')){
    function complex_encrypt($string){
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
* Simple Encrypt
*
* @return string
* @author Dimas Wicaksono
**/
if ( ! function_exists('encrypt')){
    function encrypt($string) {
        // you may change these values to your own
        $secret_key     = url_title(app()->name);
        $secret_iv      = url_title(app()->company);
    
        $output         = false;
        $encrypt_method = "AES-256-CBC";
        $key    = hash('sha256', $secret_key);
        $iv     = substr(hash('sha256', $secret_iv), 0, 16);

        $str    = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        $output = str_replace(array('+', '/', '='), array('-', '_', '~'), $str);
        
        return $output;
    }
}

/**
* Simple Decrypt
*
* @return string
* @author Dimas Wicaksono
**/
if ( ! function_exists('decrypt')){
    function decrypt($string) {
        // you may change these values to your own
        $secret_key     = url_title(app()->name);
        $secret_iv      = url_title(app()->company);
    
        $output         = false;
        $encrypt_method = "AES-256-CBC";
        $key    = hash('sha256', $secret_key);
        $iv     = substr(hash('sha256', $secret_iv), 0, 16);

        $str    = str_replace(array('-', '_', '~'), array('+', '/', '='), $string);
        $output = openssl_decrypt(base64_decode($str), $encrypt_method, $key, 0, $iv);
        
        return $output;
    }
}

/**
* Complex Decrypt
*
* @return string
* @author Dimas Wicaksono
**/
if ( ! function_exists('complex_decrypt')){
    function complex_decrypt($string){
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

/**
* Upload
*
* @return array
* @author Dimas Wicaksono
**/
if(!function_exists('do_upload'))
{
	function do_upload($identifier = NULL, $conf = array()){
		$status = 1;
		$reason = NULL;
		$CI =& get_instance();
				
		if(empty($identifier)){
			return array('status' => 0, 'data' => 'Identifier should not be empty. No files uploaded');
		}else{
			$config['overwrite'] 			= TRUE;
			$config['file_ext_tolower'] 	= TRUE;

			// limit size file
			if(array_key_exists('size', $conf)){
				$config['max_size'] 		= $conf['size']; // in kB
			}else{
				$config['max_size'] 		= ini_get("upload_max_filesize"); // maskimal dari php ini		
			}

			// default upload path
			if(array_key_exists('path', $conf)){
				// cek apakah ada folder ini? bila tidak ada buat foldernya
				if(!is_dir($conf['path'])){
					mkdir($conf['path'], 755, true);
				}
				$config['upload_path'] 		= $conf['path'];
			}else{
				// cek apakah ada folder ini? bila tidak ada buat foldernya
				if(!is_dir('./uploads')){
					mkdir('./uploads', 755);
				}
				$config['upload_path'] 		= './uploads';
			}

			// file type yang diperblehkan untuk diupload (sudah di cek mimenya juga)
			if(array_key_exists('type', $conf)){
				$config['allowed_types'] 	= $conf['type'];
			}else{
				$config['allowed_types'] 	= '*';				
			}
			
			if(array_key_exists('name', $conf)){
				$config['file_name'] 	 	= $conf['name'];
			}else{
				// nama file tidak ada, maka berikan nama random
				$config['encrypt_name'] 	= TRUE;				
			}
			
			$CI->load->library('upload');
            $CI->upload->initialize($config);

			$upload = $CI->upload->do_upload($identifier);
			if($upload){
				return array('status' => 1, 'data' => $CI->upload->data());
			}else{
				return array('status' => 0, 'data' => $CI->upload->display_errors());
			}
		}
	}
}