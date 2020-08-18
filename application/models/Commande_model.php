<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Commande_Model extends CI_Model
{
    protected $commande_table = 'aqi_pp_commande';

    public function commande($id)
    {
        $qry= $this->db->get_where($this->commande_table,array('id'=>$id));
        return $qry->row();        
    }

    public function all_commande()
    {
        $query = $this->db->get($this->commande_table);
        return $query->result_array();
    }

    /**
     * Create New commande insert
     * @param: {Array} data
     */
    public function create(array $data)
    {
        $this->db->insert($this->commande_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->commande_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->commande_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->commande_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->commande_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}