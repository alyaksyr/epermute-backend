<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Demande_Model extends CI_Model
{
    protected $demande_table = 'gp5das_demande';
    protected $select = '
        id,
        num_demande,
        annee,
        id_demandeur demandeur,
        id_iepp inspection,
        service ecole,
        localite,
        is_valide valide,
        is_active active,
        couverture,
        description
    ';


    /**
     * Get Single demande 
     * @method: GET
     */
    public function demande($id)
    {
        $qry= $this->db->get_where($this->demande_table,array('id'=>$id));
        return $qry->row();
    }

    /**
     * Get All demande 
     * @method: GET
     */
    public function all_demande()
    {
        $query = $this->db->get($this->demande_table);
        return $query->result_array();
    }

    /**
     * Get demandes with essantial informations
     * @method: GET
     * @param: User Id
     */
    public function get_demande($param='')
    {
        $this->db->select($this->select);
        $this->db->from($this->demande_table);
        if ($param === '') {
            return $this->db->get()->result();
        } else {
            $this->db->where('id',$param);
            $this->db->or_where('num_demande',$param);
            return $this->db->get()->row();
        }
        
             
    }
    /**
     * Add new demande
     * @param: {Array} demande data
     * @method: POST
     */

    public function create(array $data)
    {
        $this->db->insert($this->demande_table,$data);
        return $this->db->insert_id();
    }

    /**
     * Update demande
     * @param: {Array} demande data, {id}
     * @method: PUT
     */
    public function update(array $data)
    {
        $query = $this->db->get_where($this->demande_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->demande_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    /**
     * Delete demande
     * @param: id
     * @method: DELETE
     */
    public function delete(array $data)
    {
        $query = $this->db->get_where($this->demande_table,$data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->demande_table,$data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;
        
    }
    /**
     * Get All demande By Admin
     * @method: GET
     * @param: User Id
     */
    public function demandes_by_user($id)
    {
        $this->db->select($this->select);
        $this->db->from($this->demande_table);
        $this->db->where('id_demandeur',$id);
        $qry = $this->db->get();
        return $qry->result();        
    }

    /**
     * Get All demande By Direction
     * @method: GET
     * @param: Direction Id
     */
    public function demande_by_dren($id)
    {
        $this->db->select($this->select);
        $this->db->from($this->demande_table);
        $this->db->where('id_dren',$id);
        $qry = $this->db->get();
        return $qry->result();  
    }

    /**
     * Get All demande By Inspection
     * @method: GET
     * @param: Inspection Id
     */
    public function demande_by_iepp($id)
    {
        $this->db->select($this->select);
        $this->db->from($this->demande_table);
        $this->db->where('id_iepp',$id);
        $qry = $this->db->get();
        return $qry->result();  
    }
    
}


