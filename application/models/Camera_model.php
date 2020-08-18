<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Camera_Model extends CI_Model
{
    protected $camera_table = 'aqi_pp_camera';

    public function camera($id)
    {
        $qry= $this->db->get_where($this->camera_table,array('camera_id'=>$id));
        return $qry->row();        
    }

    public function all_camera()
    {
        $query = $this->db->get($this->camera_table);
        return $query->result_array();
    }

    /**
     * Get all camera in 
     * @param: ids: string "['','']"
     */
    public function cameras($ids)
    {
        $ids = json_decode($ids); 
        $this->db->select('camera_type,camera_unite,camera_resolution');
        $this->db->from($this->camera_table);
        $this->db->where_in('camera_id',$ids);
        $qry = $this->db->get();
        return $qry->result_array();     
    }

    /**
     * Create New camera insert
     * @param: {Array} data
     */
    public function create(array $data)
    {
        $this->db->insert($this->camera_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->camera_table,array('camera_id'=>$data['camera_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->camera_table,$data,['camera_id'=>$query->row('camera_id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->camera_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->camera_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}