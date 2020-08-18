<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categorie_Model extends CI_Model
{
    protected $categorie_table = 'aqi_pp_categorie';

    public function categorie($id)
    {
        $qry = $this->db->get_where($this->categorie_table,array('cat_id'=>$id));
        return $qry->row();        
    }

    public function all_categorie()
    {
        $query = $this->db->get($this->categorie_table);
        return $query->result_array();
    }

    public function create(array $data)
    {
        $this->db->insert($this->categorie_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->categorie_table,array('cat_id'=>$data['cat_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->categorie_table,$data,['cat_id'=>$query->row('cat_id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->categorie_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->categorie_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    /**
     * Get Libelle categorie
     * @param: id
     */
    public function categorie_libelle($id)
    {
        $this->db->select('cat_id id,cat_libelle name, cat_image image, cat_slug slug');
        $this->db->from($this->categorie_table);
        $this->db->where('cat_id',$id);
        $qry = $this->db->get();
        return $qry->row();       
    }
    
}