<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ArticleMeta_Model extends CI_Model
{
    protected $meta_table = 'aqi_pp_article_meta';

    public function meta($id)
    {
        $qry= $this->db->get_where($this->meta_table,array('id'=>$id));
        return $qry->row();       
    }

    public function meta_by_article_id($id)
    {
        $this->db->select('meta_key key, meta_value value');
        $this->db->from($this->meta_table);
        $this->db->where('id_article', $id);
        return $this->db->get()->result_array();       
    }

    public function get_meta_value_by_id_article($id){
        $this->db->select('meta_value');
        $this->db->from($this->meta_table);
        $this->db->where('id_article',$id);
        $qry = $this->db->get();
        return $qry->result();
    }

    public function all_meta()
    {
        $query = $this->db->get($this->meta_table);
        return $query->result_array();
    }

    public function create(array $data)
    {       
        $this->db->insert_batch($this->meta_table,$data);        
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->meta_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->meta_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->meta_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->meta_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;
    }
    
}