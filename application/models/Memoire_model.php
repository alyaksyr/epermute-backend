<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Memoire_Model extends CI_Model
{
    protected $memoire_table = 'aqi_pp_memoire';

    public function memoire($id)
    {
        $qry= $this->db->get_where($this->memoire_table,array('id'=>$id));
        return $qry->row();        
    }

    public function all_memoire()
    {
        $query = $this->db->get($this->memoire_table);
        return $query->result_array();
    }

    /**
     * Get all Memoire in 
     * @param: ids: string "['','']"
     */
    public function memoires($ids)
    {
        $ids = json_decode($ids); 
        $this->db->select('type,unite,capacite');
        $this->db->from($this->memoire_table);
        $this->db->where_in('id',$ids);
        $qry = $this->db->get();
        return $qry->result_array();     
    }

    /**
     * Create New memoire insert
     * @param: {Array} data
     */
    public function create(array $data)
    {
        $this->db->insert($this->memoire_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->memoire_table, array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->memoire_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->memoire_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->memoire_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}