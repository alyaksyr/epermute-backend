<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Panier_Model extends CI_Model
{
    protected $panier_table = 'aqi_pp_panier';

    public function panier($id)
    {
        $qry= $this->db->get_where($this->panier_table,array('id'=>$id));
        return $qry->row();        
    }

    public function all_panier()
    {
        $query = $this->db->get($this->panier_table);
        return $query->result_array();
    }

    /**
     * Create New panier insert
     * @param: {Array} data
     */
    public function create(array $data)
    {
        $this->db->insert($this->panier_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->panier_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->panier_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->panier_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->panier_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}