<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Produit_Model extends CI_Model
{
    protected $produit_table = 'aqi_pp_produit';

    public function produit($id)
    {
        $qry = $this->db->get_where($this->produit_table,array('produit_id'=>$id));
        return $qry->row();        
    }

    public function all_produit_troc(){
        $this->db->select('*');
        $this->db->from($this->produit_table);
        $this->db->where('produit_id.is_troc',1);
        $qry = $this->db->get();
        return $qry->result();
    }

    public function all_produit()
    {
        $query = $this->db->get($this->produit_table);
        return $query->result();
    }
    /**
     * Get all product with minimum informations
     */

    public function get_products($param=''){
        $select ='produit_id id,produit_libelle name,produit_slug slug, produit_quantite quantity, produit_prix price,';
        $select .='produit_couleur couleur, produit_prix_promo old_price,produit_main_image image,';
        $select .='produit_etat state,produit_is_dispo availability, produit_is_troc exchangebility,produit_status status,';
        $select .='produit_images images,produit_id_boutique boutique,produit_id_article article';
        $this->db->select($select);
        $this->db->from($this->produit_table);
        if ($param ==='') {
            $query = $this->db->get();
            return $query->result();
        } else {
            $this->db->where('produit_id',$param);
            $this->db->or_where('produit_slug',$param);
            $query = $this->db->get();
            return $query->row();
        }
        
    }

    public function get_product($slug=False){
        $this->db->select('*');
        $this->db->from('aqi_pp_produit p');
        $this->db->join('aqi_pp_article a','p.produit_id_article = a.art_id');
        $this->db->join('aqi_pp_categorie c','c.cat_id = a.art_id_categorie');
        $this->db->join('aqi_pp_sous_categorie sc','sc.scat_id = a.art_id_sous_categorie');
        $this->db->join('aqi_pp_article_meta ct','ct.id_article = a.art_id','outer left');
        if ($slug === false) {
            $query = $this->db->get();
            return $query->result();
        } else {
            $this->db->where('p.produit_slug',$slug);
            $query = $this->db->get();
            return $query->row();
        }
        
    }

    public function create(array $data)
    {
        $this->db->insert($this->produit_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->produit_table,array('produit_id'=>$data['produit_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->produit_table,$data,['produit_id'=>$query->row('produit_id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->produit_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->produit_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;
    }
    
}
