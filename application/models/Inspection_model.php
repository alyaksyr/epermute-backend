<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Inspection_Model extends CI_Model
{
    protected $inspection_table = 'gp5das_iepp';

    public function inspection($id)
    {
        $qry= $this->db->get_where($this->inspection_table,array('id'=>$id));
        return $qry->row();        
    }

    public function all_inspection()
    {
        $query = $this->db->get($this->inspection_table);
        return $query->result();
    }

    /**
     * Get all inspection in 
     * @param: ids: string "['','']"
     */
    public function inspection_dren($id)
    { 
        $this->db->select('id, id_dren dren,nom inspection,contact,email,ville');
        $this->db->from($this->inspection_table);
        $this->db->where('id',$id);
        $qry = $this->db->get();
        return $qry->row();     
    }

    /**
     * Create New inspection insert
     * @param: {Array} data
     */
    public function create(array $data)
    {
        $this->db->insert($this->inspection_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->inspection_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->inspection_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->inspection_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->inspection_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}