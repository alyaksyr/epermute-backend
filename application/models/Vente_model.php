<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Vente_Model extends CI_Model
{
    protected $vente_table = 'aqi_pp_vente';

    public function vente($id)
    {
        $qry= $this->db->get_where($this->vente_table,array('id'=>$id));
        return $qry->row();        
    }

    public function all_vente()
    {
        $query = $this->db->get($this->vente_table);
        return $query->result_array();
    }

    /**
     * Create New vente insert
     * @param: {Array} data
     */
    public function create(array $data)
    {
        $this->db->insert($this->vente_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->vente_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->vente_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->vente_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->vente_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}