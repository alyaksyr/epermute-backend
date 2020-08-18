<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Modele_Model extends CI_Model
{
    protected $modele_table = 'aqi_pp_modele';

    public function modele($id)
    {
        $qry= $this->db->get_where($this->modele_table,array('modele_id'=>$id));
        return $qry->row();        
    }

    public function all_modele()
    {
        $query = $this->db->get($this->modele_table);
        return $query->result_array();
    }

    public function create(array $data)
    {
        $this->db->insert($this->modele_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->modele_table, array('modele_id'=>$data['modele_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->modele_table,$data,['modele_id'=>$query->row('modele_id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->modele_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->modele_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}