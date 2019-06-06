<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pays_Model extends CI_Model
{
    protected $pays_table = 'aqi_pp_pays';

    public function pays($id)
    {
        $qry = $this->db->get_where($this->pays_table,array('id'=>$id));
        return $qry->row();        
    }

    public function all_pays()
    {
        $query = $this->db->get($this->pays_table);
        return $query->result_array();
    }

    public function create(array $data)
    {
        $this->db->insert($this->pays_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data, $id)
    {
        $query = $this->db->get_where($this->pays_table,array('id'=>$id));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->pays_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->pays_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->pays_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}