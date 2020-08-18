<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Comment_Model extends CI_Model
{
    protected $comment_table = 'aqi_pp_comment';

    public function comment($id)
    {
        $qry= $this->db->get_where($this->comment_table,array('cmt_id'=>$id));
        return $qry->row();        
    }

    public function all_comment()
    {
        $query = $this->db->get($this->comment_table);
        return $query->result_array();
    }

    /**
     * Create New comment insert
     * @param: {Array} data
     */
    public function create(array $data)
    {
        $this->db->insert($this->comment_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->comment_table,array('cmt_id'=>$data['cmt_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->comment_table,$data,['cmt_id'=>$query->row('cmt_id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->comment_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->comment_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }

    public function get_comment($id){
        $this->db->select('cmt_rate');
        $this->db->from($this->comment_table);
        $this->db->where('cmt_id_product',$id);
        return $this->db->count_all_results();
    }

    public function get_avg_comment($id){
        $this->db->select_avg('cmt_rate');
        $this->db->where('cmt_id_product',$id);
        $this->db->from($this->comment_table);
        return (float)$this->db->get()->row()->cmt_rate;
    }
    
}