<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Etat_Model extends CI_Model
{
    protected $etat_table = 'aqi_pp_ecran';

    public function ecran($id)
    {
        $qry = $this->db->get_where($this->etat_table,array('ecran_id'=>$id));
        return $qry->row();        
    }

    public function all_ecran()
    {
        $query = $this->db->get($this->etat_table);
        return $query->result_array();
    }

    public function create(array $data)
    {
        $this->db->insert($this->etat_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->etat_table,array('ecran_id'=>$data['ecran_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->etat_table,$data,['ecran_id'=>$query->row('ecran_id')]);
        } 
        return false;
    }

    public function delete($id)
    {
        $query = $this->db->get_where($this->etat_table, array('ecran_id'=>$id));
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->etat_table, array('ecran_id'=>$id));
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}