<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pays_Model extends CI_Model
{
    protected $pays_table = 'gp5das_pays';

    public function pays($id)
    {
        $qry = $this->db->get_where($this->pays_table,array('pays_id'=>$id));
        return $qry->row();        
    }

    public function all_pays()
    {
        $query = $this->db->get($this->pays_table);
        return $query->result_array();
    }

    public function create(array $data)
    {
        $this->db->insert($this->pays_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->pays_table,array('pays_id'=>$data['pays_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->pays_table,$data,['pays_id'=>$query->row('pays_id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->pays_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->pays_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }

    public function get_pays($param = '')
    {
        $this->db->select('pays_id id,pays_libelle name, pays_slug slug, pays_logo flag,pays_indicatif indicatif');
        $this->db->from($this->pays_table);
        if ($param === '') {
            return $this->db->get()->result();
        } else {
            $this->db->where('pays_id', $param);
            $this->db->or_where('pays_slug', $param);
            return $this->db->get()->row();
        }      
    }
    
}