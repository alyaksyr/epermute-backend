<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Telephone_Model extends CI_Model
{
    protected $telephone_table = 'aqi_pp_telephone';
    

    public function valid_code($code){
        $qry = $this->db->get_where($this->telephone_table, array('code'=>$code));
        return ($qry->num_rows() <= 0)?'true':'false';              
    }

    public function get_telephone($str)
    {
        $this->db->select('*');
        $this->db->from($this->telephone_table);
        $this->db->where('id_article',$str);
        $this->db->or_where('code',$str);
        $qry = $this->db->get();
        return $qry->result_array();        
    }

    public function get_telephones()
    {
        $query = $this->db->get($this->telephone_table);
        return $query->result_array();
    }

    public function get_article_telephone()
    {

    }

    /**
     * Add new Telephone
     * @param: {Array} Telephone data
     * @method: POST
     */
    public function create(array $data)
    {
        $this->db->insert($this->telephone_table,$data);
        return $this->db->insert_id();
    }

    /**
     * Update Telephone
     * @param: {Array} Telephone data, {id}
     * @method: PUT
     */
    public function update(array $data, $id)
    {
        $query = $this->db->get_where($this->telephone_table,array('id'=>$id));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->telephone_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function get_marque($id)
    {
        $this->db->select('libelle');
        $this->db->from('aqi_pp_marque');
        $this->db->where('id',$id);
        $qry = $this->db->get();  
        return $qry->row();  
    }

    public function get_modele($id)
    {
        $this->db->select('libelle');
        $this->db->from('aqi_pp_modele');
        $this->db->where('id',$id);
        $qry = $this->db->get();  
        return $qry->row();  
    }

    public function get_reseau($id)
    {
        $this->db->select('libelle');
        $this->db->from('aqi_pp_reseau');
        $this->db->where('id',$id);
        $qry = $this->db->get();  
        return $qry->row();  
    }

    public function get_ecran($id)
    {
        $this->db->select('taille,unite');
        $this->db->from('aqi_pp_ecran');
        $this->db->where('id',$id);
        $qry = $this->db->get();  
        return $qry->row();  
    }

    public function get_systeme($id)
    {
        $this->db->select('type,libelle,version');
        $this->db->from('aqi_pp_systeme');
        $this->db->where('id',$id);
        $qry = $this->db->get();  
        return $qry->row();  
    }

    public function get_memoire($ids)
    {
        $memoire_data = array();
        $ids = json_decode($ids); 
        $this->db->select('type,unite,capacite');
        $this->db->from('aqi_pp_memoire');
        $this->db->where_in('id',$ids);
        $qry = $this->db->get();
        foreach($qry->result_array() as $row) {
            $memoire_data[]=$row;
        }    
        return $memoire_data;  
    }

    public function get_categorie_telephone($id)
    {
        $this->db->select('libelle');
        $this->db->from('aqi_pp_categorie_telephone');
        $this->db->where('id',$id);
        $qry = $this->db->get();
           
        return $qry->row();  
    }

    public function get_camera($ids)
    {
        $camera_data = array();
        $ids = json_decode($ids); 
        $this->db->select('type,unite,resolution');
        $this->db->from('aqi_pp_camera');
        $this->db->where_in('id',$ids);
        $qry = $this->db->get();
        foreach($qry->result_array() as $row) {
            $camera_data[]=$row;
        }    
        return $camera_data; 
    }
    
}