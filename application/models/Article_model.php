<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Article_Model extends CI_Model
{
    protected $article_table = 'aqi_pp_article';

    public function article($id)
    {
        $qry= $this->db->get_where($this->article_table,array('id'=>$id));
        return $qry->row();       
    }

    public function article_by_code($code)
    {
        $qry= $this->db->get_where($this->article_table,array('code'=>$code));
        return $qry->row();       
    }

    public function all_article()
    {
        $query = $this->db->get($this->article_table);
        return $query->result();
    }

    public function create(array $data)
    {       
        $this->db->insert($this->article_table,$data);        
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->article_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->article_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->article_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->article_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;
    }

    /**
     * Get all infos article
     * @param: {Id, code}
    */
    public function get_detail_article($param)
    {
        $this->db->select('*');
        $this->db->from($this->article_table);
        $this->db->join('aqi_pp_article_meta', 'aqi_pp_article_meta.id_article = aqi_pp_article.id');
        $this->db->where('aqi_pp_article.id',$param);
        $this->db->or_where('aqi_pp_article.code',$param);
        $query = $this->db->get();
        return $query->row();
    }
    
}