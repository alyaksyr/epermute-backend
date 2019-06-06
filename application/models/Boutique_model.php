<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Boutique_Model extends CI_Model
{
    protected $boutique_table = 'aqi_pp_boutique';


    /**
     * Get Single Boutique 
     * @method: GET
     */
    public function boutique($id)
    {
        $qry= $this->db->get_where($this->boutique_table,array('id'=>$id));
        return $qry->row();
    }

    /**
     * Get All Boutique 
     * @method: GET
     */
    public function all_boutique()
    {
        $query = $this->db->get($this->boutique_table);
        return $query->result_array();
    }

    /**
     * Add new Boutique
     * @param: {Array} Boutique data
     * @method: POST
     */

    public function create(array $data)
    {
        $this->db->insert($this->boutique_table,$data);
        return $this->db->insert_id();
    }

    /**
     * Update boutique
     * @param: {Array} Boutique data, {id}
     * @method: PUT
     */
    public function update(array $data)
    {
        $query = $this->db->get_where($this->boutique_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->boutique_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    /**
     * Delete boutique
     * @param: id
     * @method: DELETE
     */
    public function delete(array $data)
    {
        $query = $this->db->get_where($this->boutique_table,$data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->boutique_table,$data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;
        
    }
    /**
     * Get All Boutique By Admin
     * @method: GET
     * @param: User Id
     */
    public function boutiques_by_admin($str)
    {
        $this->db->select('*');
        $this->db->from($this->boutique_table);
        $this->db->join('aqi_pp_users','aqi_pp_users.id=aqi_pp_boutique.id_admin');
        $qry = $this->db->get();
        return $qry->result_array();        
    }

    /**
     * Get All Boutique By Pays
     * @method: GET
     * @param: User Id
     */
    public function boutique_by_pays($id)
    {
        $this->db->select('*');
        $this->db->from($this->boutique_table);
        $this->db->join('aqi_pp_pays','aqi_pp_pays.id=aqi_pp_boutique.id_pays');
        $qry = $this->db->get();
        return $qry->result_array();  
    }

    /**
     * Get All Boutique By Ville
     * @method: GET
     * @param: User Id
     */
    public function boutique_by_ville($id)
    {
        $this->db->select('*');
        $this->db->from($this->boutique_table);
        $this->db->join('aqi_pp_ville','aqi_pp_ville.id=aqi_pp_boutique.id_ville');;
        $qry = $this->db->get();
        return $qry->result_array();  
    }
    
}

