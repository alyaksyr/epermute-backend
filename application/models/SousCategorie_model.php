<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SousCategorie_Model extends CI_Model
{
    protected $sous_categorie_table = 'aqi_pp_sous_categorie';

    public function sous_categorie($id)
    {
        $qry = $this->db->get_where($this->sous_categorie_table,array('scat_id'=>$id));
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
        $query = $this->db->get_where($this->sous_categorie_table,array('scat_id'=>$data['scat_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->sous_categorie_table,$data,['scat_id'=>$query->row('scat_id')]);
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

    public function sous_categorie_site($id)
    {
        $this->db->select('scat_id id,scat_libelle name,scat_slug slug,scat_image image');
        $this->db->from($this->sous_categorie_table);
        $this->db->where('scat_id', $id);
        $qry = $this->db->get();
        return $qry->row();        
    }
    
}