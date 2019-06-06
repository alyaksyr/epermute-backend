<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ville_Model extends CI_Model
{
    protected $ville_table = 'aqi_pp_ville';

    public function ville($id)
    {
        $qry = $this->db->get_where($this->ville_table,array('id'=>$id));
        return $qry->row();        
    }

    public function ville_by_code($code)
    {
        $qry = $this->db->get_where($this->ville_table,array('code'=>$code));
        return ($qry->num_rows() <= 0)? true: false;       
    }

    public function ville_by_pays($id)
    {
        $qry = $this->db->get_where($this->ville_table,array('id'=>$id));
        return $qry->result();  
    }

    public function all_villes()
    {
        $query = $this->db->get($this->ville_table);
        return $query->result();
    }

    public function create(array $data)
    {
        $this->db->insert($this->ville_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->ville_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->ville_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->ville_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->ville_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}