<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ville_Model extends CI_Model
{
    protected $ville_table = 'gp5das_ville';

    public function ville($id)
    {
        $qry = $this->db->get_where($this->ville_table,array('ville_id'=>$id));
        return $qry->row();        
    }

    public function ville_by_code($code)
    {
        $qry = $this->db->get_where($this->ville_table,array('ville_code'=>$code));
        return ($qry->num_rows() <= 0)? true: false;       
    }

    public function ville_pays($id)
    {
        $this->db->select('ville_id,ville_code,ville_libelle,p.*');
        $this->db->from('aqi_pp_ville as v');
        $this->db->join('aqi_pp_pays as p','v.id_pays = p.pays_id','left');
        $this->db->where(array('v.ville_id'=>$id));
        return $this->db->get()->row();
  
    }

    public function all_ville_pays()
    {
        $this->db->select('ville_id,ville_code,ville_libelle,p.*');
        $this->db->from('aqi_pp_ville as v');
        $this->db->join('aqi_pp_pays as p','v.id_pays = p.pays_id','left');
        return $this->db->get()->result();
  
    }

    public function ville_by_pays($id)
    {
        $qry = $this->db->get_where($this->ville_table,array('id_pays'=>$id));
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
        $query = $this->db->get_where($this->ville_table,array('ville_id'=>$data['ville_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->ville_table,$data,['ville_id'=>$query->row('ville_id')]);
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

    public function get_ville($param = '')
    {
        $this->db->select('ville_id id,ville_libelle name, ville_slug slug, id_pays pays');
        $this->db->from($this->ville_table);
        if ($param === '') {
            return $this->db->get()->result();
        } else {
            $this->db->where('ville_id', $param);
            $this->db->or_where('ville_slug', $param);
            return $this->db->get()->row();
        }      
    }
    
}