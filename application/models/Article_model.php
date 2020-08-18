<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Article_Model extends CI_Model
{
    protected $article_table = 'aqi_pp_article';

    public function article($id)
    {
        $qry= $this->db->get_where($this->article_table,array('art_id'=>$id));
        return $qry->row();       
    }

    public function article_by_code($code)
    {
        $qry= $this->db->get_where($this->article_table,array('art_code'=>$code));
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
        $query = $this->db->get_where($this->article_table,array('art_id'=>$data['art_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->article_table,$data,['art_id'=>$query->row('art_id')]);
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
     * Get minimum infos article
     * @param: {Id, code}
    */
    public function get_article($param = '')
    {
        $select = 'art_id id, art_code code,art_libelle name,art_fabricant fabricant,art_prix default_price,';
        $select .='art_slug slug,art_image image, art_qte_package quantity,';
        $select .='art_id_categorie categorie, art_id_sous_categorie sous_categorie, art_id_marque marque';
        $this->db->select($select);
        $this->db->from($this->article_table);
        if ($param === '') {
            return $this->db->get()->result();
        }

        $this->db->where('art_id',$param);
        $this->db->or_where('art_code',$param);
        return $this->db->get()->row();
    }

        /**
     * Get all infos article
     * @param: {Id, code}
    */
    public function get_detail_article($param)
    {
        $this->db->select('*');
        $this->db->from($this->article_table);
        $this->db->join('aqi_pp_article_meta', 'aqi_pp_article_meta.id_article = aqi_pp_article.art_id');
        $this->db->where('aqi_pp_article.art_id',$param);
        $this->db->or_where('aqi_pp_article.art_code',$param);
        $query = $this->db->get();
        return $query->row();
    }
    
}