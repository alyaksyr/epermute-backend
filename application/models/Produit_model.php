<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Produit_Model extends CI_Model
{
    protected $produit_table = 'aqi_pp_produit';

    public function produit($id)
    {
        $qry = $this->db->get_where($this->produit_table,array('id'=>$id));
        return $qry->row();        
    }

    public function all_produit()
    {
        $query = $this->db->get($this->produit_table);
        return $query->result();
    }

    public function create(array $data)
    {
        $this->db->insert($this->produit_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->produit_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->produit_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->produit_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->produit_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;
    }
    
}