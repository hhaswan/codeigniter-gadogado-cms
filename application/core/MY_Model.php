<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_model extends CI_Model{
    
    /**
    * Get Entry
    *
    * @return object
    * @author Dimas Wicaksono
    **/
    public function get($table, $args = [], $like = [], $order_by = [], $limit = null, $offset = null){

        $output = false;
        
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
    * Insert Entry
    *
    * @return integer
    * @author Dimas Wicaksono
    **/
    public function insert($table, $data = null){
        
        // -1 untuk menunjukkan kalo operasi ini gagal/fail
        $output = -1;
        
        // mencegah tidak ada item dimasukkan
        if(empty($data)){
            return false;
        }elseif(is_array($data)){
            $query = $this->db->insert($table, $data);
            if($query){
                if($this->db->insert_id() == null || $this->db->insert_id() == 0){
                    // 0 berhasil tanpa ada insert_id nya
                    $output = 0;
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
    public function delete($table, $args = null){

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
    public function update($table, $args = null, $data = null){
        
        $output = false;
        
        // mencegah tidak adanya id yang dijadikan acuan untuk diupdate
        if(empty($args)){
            return false;
        }elseif(is_array($args)){
            $query = $this->db->update($this->table, $data, $args);
            if($query){
                $output = true;
            }

            return $output;
        }

    }
}