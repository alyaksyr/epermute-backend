<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ville_Model extends CI_Model
{
    protected $ville_table = 'aqi_pp_ville';

    public function valid_code($code){
        $qry = $this->db->get_where($this->ville_table, array('code'=>$code));
        return ($qry->num_rows() <= 0)?'true':'false';              
    }

    public function get_ville($str)
    {
        $this->db->select('*');
        $this->db->from($this->ville_table);
        $this->db->where('id',$str);
        $this->db->or_where('code',$str);
        $qry = $this->db->get();
        return $qry->row();        
    }

    public function get_ville_by_id_pays($str)
    {
        $this->db->select('*');
        $this->db->from($this->ville_table);
        $this->db->where('id_pays',$str);
        $qry = $this->db->get();
        foreach($qry->result() as $row) {
            $ville_data[]=$row;
        }    
        return $ville_data;  
    }

    public function fetch_all_villes()
    {
        $query = $this->db->get($this->ville_table);
        foreach ($query->result() as $row) {
            $ville_data[]=$row;
        }
        return $ville_data;
    }

    public function insert_ville(array $data)
    {
        return $this->db->insert($this->ville_table,  $data);
    }

    public function update_ville($id)
    {
        $ville = $this->get_ville($id);
        $this->db->where('id', $id);
        return $this->db->update($this->ville_table,$ville);
    }
    
}