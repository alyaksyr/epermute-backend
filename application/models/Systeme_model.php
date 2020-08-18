<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Systeme_Model extends CI_Model
{
    protected $systeme_table = 'aqi_pp_systeme';

    public function systeme($id)
    {
        $qry = $this->db->get_where($this->systeme_table,array('systeme_id'=>$id));
        return $qry->row();        
    }

    public function all_systeme()
    {
        $query = $this->db->get($this->systeme_table);
        return $query->result_array();
    }

    public function create(array $data)
    {
        $this->db->insert($this->systeme_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->systeme_table, array('systeme_id'=>$data['systeme_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->systeme_table,$data,['systeme_id'=>$query->row('systeme_id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->systeme_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->systeme_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}