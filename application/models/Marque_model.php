<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Marque_Model extends CI_Model
{
    protected $marque_table = 'aqi_pp_marque';

    public function marque($id)
    {
        $qry = $this->db->get_where($this->marque_table,array('id'=>$id));
        return $qry->row();
    }

    public function all_marque()
    {
        $query = $this->db->get($this->marque_table);
        return $query->result_array();
    }

    public function create(array $data)
    {
        $this->db->insert($this->marque_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->marque_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->marque_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->marque_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->marque_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;
    }

    /**
     * Get Mrque libelle
     * @param: id
     */
    public function marque_libelle($id)
    {
        $this->db->select('id,libelle');
        $this->db->from($this->marque_table);
        $this->db->where('id',$id);
        $qry = $this->db->get();
        return $qry->row();       
    }
}