<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Direction_Model extends CI_Model
{
    protected $direction_table = 'gp5das_dren';

    public function direction($id)
    {
        $qry= $this->db->get_where($this->direction_table,array('id'=>$id));
        return $qry->row();        
    }

    public function direction_detail($id)
    {
        $this->db->select('id, nom direction, ville, contact, email');
        $this->db->from($this->direction_table);
        $this->db->where('id',$id);
        $qry = $this->db->get();
        return $qry->row();        
    }

    public function all_direction()
    {
        $query = $this->db->get($this->direction_table);
        return $query->result_array();
    }

    /**
     * Create New direction insert
     * @param: {Array} data
     */
    public function create(array $data)
    {
        $this->db->insert($this->direction_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->direction_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->direction_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->direction_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->direction_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}