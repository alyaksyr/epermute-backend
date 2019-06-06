<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SousCategorie_Model extends CI_Model
{
    protected $sous_categorie_table = 'aqi_pp_sous_categorie';

    public function sous_categorie($id)
    {
        $qry = $this->db->get_where($this->sous_categorie_table,array('id'=>$id));
        return $qry->row();        
    }

    public function all_sous_categorie()
    {
        $query = $this->db->get($this->sous_categorie_table);
        return $query->result_array();
    }

    public function create(array $data)
    {
        $this->db->insert($this->sous_categorie_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->sous_categorie_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->sous_categorie_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->sous_categorie_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->sous_categorie_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}