<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Troc_Model extends CI_Model
{
    protected $troc_table = 'aqi_pp_troc';

    public function valid_code($code){
        $qry = $this->db->get_where($this->troc_table, array('code'=>$code));
        return ($qry->num_rows() <= 0)?'true':'false';              
    }

    public function get_troc($str)
    {
        $this->db->select('*');
        $this->db->from($this->troc_table);
        $this->db->where('id',$str);
        $this->db->or_where('code',$str);
        $qry = $this->db->get();
        return $qry->row();        
    }

    public function get_trocs()
    {
        $query = $this->db->get($this->troc_table);
        return $query->result();
    }

    public function create(array $data)
    {
        $this->db->insert($this->troc_table,$data);
        return $this->db->insert_id();
    }

    public function update($id, array $data)
    {
        $query = $this->db->get_where($this->troc_table,array('id'=>$id));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->troc_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function get_marque($id)
    {
        $this->db->select('*');
        $this->db->from('aqi_pp_marque');
        $this->db->where('marque_id',$id);
        $qry = $this->db->get();  
        return $qry->row();  
    }

    public function get_modele($id)
    {
        $this->db->select('*');
        $this->db->from('aqi_pp_modele');
        $this->db->where('modele_id',$id);
        $qry = $this->db->get();  
        return $qry->row();  
    }

    public function get_reseau($id)
    {
        $this->db->select('*');
        $this->db->from('aqi_pp_reseau');
        $this->db->where('reseau_id',$id);
        $qry = $this->db->get();  
        return $qry->row();  
    }

    public function get_ecran($id)
    {
        $this->db->select('*');
        $this->db->from('aqi_pp_ecran');
        $this->db->where('ecran_id',$id);
        $qry = $this->db->get();  
        return $qry->row();  
    }

    public function get_systeme($id)
    {
        $this->db->select('*');
        $this->db->from('aqi_pp_systeme');
        $this->db->where('systeme_id',$id);
        $qry = $this->db->get();  
        return $qry->row();  
    }

    public function get_memoire($ids)
    {
        $memoire_data = array();
        $ids = (is_json($ids))?json_decode($ids):$ids; 
        $this->db->select('memoire_type, memoire_capacite, memoire_unite');
        $this->db->from('aqi_pp_memoire');
        $this->db->where_in('memoire_id',$ids);
        $qry = $this->db->get();
        foreach($qry->result() as $row) {
            $memoire_data[]=$row;
        }    
        return $memoire_data;  
    }

    public function get_camera($ids)
    {
        $camera_data = array();
        $ids = (is_json($ids))?json_decode($ids):$ids; 
        $this->db->select('camera_type, camera_resolution, camera_unite');
        $this->db->from('aqi_pp_camera');
        $this->db->where_in('camera_id',$ids);
        $qry = $this->db->get();
        foreach($qry->result() as $row) {
            $camera_data[]=$row;
        }    
        return $camera_data; 
    }
    
}