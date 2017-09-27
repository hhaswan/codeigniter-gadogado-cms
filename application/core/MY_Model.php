<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_model extends CI_Model{

    protected $table = null;
    
    /**
    * Get Entry
    *
    * @return object
    * @author Dimas Wicaksono
    **/
    public function get($table = null, $args = [], $like = [], $order_by = [], $group_by = null, $limit = null, $offset = null){

        $output = false;

        if(empty($table)){
            $table = $this->table;
        }
        
        // where clause
        if(! empty($args)){            
            $this->db->where($args);
        }

        // like clause
        if(! empty($like)){
            if(count($like) > 1){
                // bila argument like lebih dari satu
                $i = 0;
                foreach($like as $key => $row){
                    if($i == 0){
                        $this->db->like($key, $row);
                    }else{
                        $this->db->or_like($key, $row);
                    }

                    $i++;
                }
            }else{
                $this->db->like($like);
            }
        }

        // group_by
        if(! empty($group_by)){
            $this->db->group_by($group_by); 
        }

        // order by clause
        if(! empty($order_by)){
            foreach($order_by as $key => $row){
                $this->db->order_by($key, $row);
            }
        }

        // limit & offset clause
        if(! empty($limit)){
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get($table);
        if($query){
            $output = $query->result();
        }

        return $output;
    }

    /**
    * Get Entry (Count Row)
    *
    * @return object
    * @author Dimas Wicaksono
    **/
    public function get_count($table = null, $args = [], $like = [], $group_by = null, $limit = null, $offset = null){
        
        $output = false;

        if(empty($table)){
            $table = $this->table;
        }
        
        // where clause
        if(! empty($args)){            
            $this->db->where($args);
        }

        // like clause
        if(! empty($like)){
            if(count($like) > 1){
                // bila argument like lebih dari satu
                $i = 0;
                foreach($like as $key => $row){
                    if($i == 0){
                        $this->db->like($key, $row);
                    }else{
                        $this->db->or_like($key, $row);
                    }

                    $i++;
                }
            }else{
                $this->db->like($like);
            }
        }

        // group_by
        if(! empty($group_by)){
            $this->db->group_by($group_by); 
        }

        // limit & offset clause
        if(! empty($limit)){
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get($table);
        if($query){
            $output = $query->num_rows();
        }

        return $output;
    }

    /**
    * Get Entry (Count Row)
    *
    * @return object
    * @author Dimas Wicaksono
    **/
    public function get_field_data($table = null){
        
        $output = false;

        if(empty($table)){
            $table = $this->table;
        }

        $output = $this->db->field_data($table);

        return $output;
    }

    /**
    * Insert Entry
    *
    * @return integer
    * @author Dimas Wicaksono
    **/
    public function insert($table = null, $data = null){
        
        if(empty($table)){
            $table = $this->table;
        }

        // 0 untuk menunjukkan kalo operasi ini gagal/fail
        $output = 0;
        
        // mencegah tidak ada item dimasukkan
        if(empty($data)){
            return false;
        }elseif(is_array($data)){
            $query = $this->db->insert($table, $data);
            if($query){
                if($this->db->insert_id() == null || $this->db->insert_id() == 0){
                    // 0 berhasil tanpa ada insert_id nya
                    $output = true;
                }else{
                    $output = $this->db->insert_id();
                }
                return $output;
            }
        }

    }

    /**
    * Delete Entry
    *
    * @return boolean
    * @author Dimas Wicaksono
    **/
    public function delete($table = null, $args = null){
        
        if(empty($table)){
            $table = $this->table;
        }

        $output = false;
        
        // mencegah delete all
        if(empty($args)){
            return false;
        }elseif(is_array($args)){
            $query = $this->db->delete($table, $args);
            if($query){
                $output = true;
            }

            return $output;
        }

    }

    /**
    * Update Entry
    *
    * @return boolean
    * @author Dimas Wicaksono
    **/
    public function update($table = null, $args = null, $data = null){
        
        if(empty($table)){
            $table = $this->table;
        }
        
        $output = false;
        
        // mencegah tidak adanya id yang dijadikan acuan untuk diupdate
        if(empty($args)){
            return false;
        }elseif(is_array($args)){
            $query = $this->db->update($table, $data, $args);
            if($query){
                $output = true;
            }

            return $output;
        }

    }
}