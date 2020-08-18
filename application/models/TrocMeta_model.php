<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TrocMeta_Model extends CI_Model
{
    protected $condition_troc_table = 'aqi_pp_condition_troc';

    public function condition_troc($id)
    {
        $qry= $this->db->get_where($this->condition_troc_table,array('id'=>$id));
        return $qry->row();       
    }

    public function condition_troc_by_produit_id($id)
    {
        $this->db->select('*');
        $this->db->from('aqi_pp_condition_troc as ct');
        $this->db->join('aqi_pp_marque as ma', 'ma.marque_id = ct.id_marque');
        $this->db->join('aqi_pp_modele as mo', 'mo.modele_id = ct.id_modele');
        $qry = $this->db->get();
        return $qry->result_array();      
    }

    public function all_condition_troc()
    {
        $query = $this->db->get($this->condition_troc_table);
        return $query->result_array();
    }

    public function create(array $data)
    {       
        $this->db->insert_batch($this->condition_troc_table,$data);        
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->condition_troc_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->condition_troc_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->condition_troc_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->condition_troc_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;
    }
    
}