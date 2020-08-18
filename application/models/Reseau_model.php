<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reseau_Model extends CI_Model
{
    protected $reseau_table = 'aqi_pp_reseau';

    public function reseau($id)
    {
        $qry= $this->db->get_where($this->reseau_table,array('reseau_id'=>$id));
        return $qry->row();        
    }

    public function all_reseau()
    {
        $query = $this->db->get($this->reseau_table);
        return $query->result_array();
    }

    /**
     * Get all reseau in 
     * @param: ids: string "['','']"
     */
    public function reseaux($ids)
    {
        $ids = json_decode($ids); 
        $this->db->select('reseau_libelle');
        $this->db->from($this->reseau_table);
        $this->db->where_in('reseau_id',$ids);
        $qry = $this->db->get();
        return $qry->result_array();     
    }

    /**
     * Create New reseau insert
     * @param: {Array} data
     */
    public function create(array $data)
    {
        $this->db->insert($this->reseau_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->reseau_table, array('reseau_id'=>$data['reseau_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->reseau_table,$data,['reseau_id'=>$query->row('reseau_id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->reseau_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->reseau_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}