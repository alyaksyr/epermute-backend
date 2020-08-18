<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Boutique_Model extends CI_Model
{
    protected $boutique_table = 'aqi_pp_boutique';
    protected $select = '
    bqe_id id,
    bqe_code code,
    bqe_raison_social name,
	bqe_mobile mobile,
	bqe_telephone telephone,
	bqe_id_pays pays,
    bqe_id_ville ville,
    bqe_quartier quartier,
	bqe_boite_postale boite_postale,
    bqe_situation addresse,
    bqe_id_admin admin,
	bqe_sigle sigle,
    bqe_logo logo,
	bqe_slug slug,
	bqe_site_web site_web,
	bqe_email email,
	bqe_latitude latitude,
	bqe_longitude longititude';


    /**
     * Get Single Boutique 
     * @method: GET
     */
    public function boutique($id)
    {
        $qry= $this->db->get_where($this->boutique_table,array('bqe_id'=>$id));
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
     * Get Boutiques with essantial informations
     * @method: GET
     * @param: User Id
     */
    public function get_boutique($param='')
    {
        $this->db->select($this->select);
        $this->db->from($this->boutique_table);
        if ($param === '') {
            return $this->db->get()->result();
        } else {
            $this->db->where('bqe_id',$param);
            $this->db->or_where('bqe_slug',$param);
            return $this->db->get()->row();
        }
        
             
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
        $query = $this->db->get_where($this->boutique_table,array('bqe_id'=>$data['bqe_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->boutique_table,$data,['bqe_id'=>$query->row('bqe_id')]);
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
    public function boutiques_by_admin($id)
    {
        $this->db->select($this->select);
        $this->db->from($this->boutique_table);
        $this->db->where('bqe_id_admin',$id);
        $qry = $this->db->get();
        return $qry->result();        
    }

    /**
     * Get All Boutique By Pays
     * @method: GET
     * @param: User Id
     */
    public function boutique_by_pays($id)
    {
        $this->db->select($this->select);
        $this->db->from($this->boutique_table);
        $qry = $this->db->get();
        return $qry->result();  
    }

    /**
     * Get All Boutique By Ville
     * @method: GET
     * @param: User Id
     */
    public function boutique_by_ville($id)
    {
        $this->db->select($this->select);
        $this->db->from($this->boutique_table);
        $qry = $this->db->get();
        return $qry->result();  
    }
    
}


