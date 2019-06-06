<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ecran_Model extends CI_Model
{
    protected $ecran_table = 'aqi_pp_ecran';

    public function ecran($id)
    {
        $qry = $this->db->get_where($this->ecran_table,array('id'=>$id));
        return $qry->row();        
    }

    public function all_ecran()
    {
        $query = $this->db->get($this->ecran_table);
        return $query->result_array();
    }

    public function create(array $data)
    {
        $this->db->insert($this->ecran_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data, $id)
    {
        $query = $this->db->get_where($this->ecran_table,array('id'=>$id));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->ecran_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete($id)
    {
        $query = $this->db->get_where($this->ecran_table, array('id'=>$id));
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->ecran_table, array('id'=>$id));
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}