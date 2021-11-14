<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Fiche_Model extends CI_Model
{
    protected $fiche_table = 'gp5das_fiche';

    public function fiche($id)
    {
        $qry = $this->db->get_where($this->fiche_table,array('id'=>$id));
        return $qry->row();        
    }

    public function all_fiche_by_user($id){
        $this->db->select('id, num_fiche numero, annee_scolaire annee,num_demande demande, id_demandeur demandeur, id_recepteur recepteur');
        $this->db->from($this->fiche_table);
        $this->db->where('id_demandeur',$id);
        $this->db->or_where('id_recepteur',$id);
        $qry = $this->db->get();
        return $qry->result();
    }

    public function all_fiche()
    {
        $query = $this->db->get($this->fiche_table);
        return $query->result();
    }


    public function create(array $data)
    {
        $this->db->insert($this->fiche_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->fiche_table,array('id'=>$data['id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->fiche_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->fiche_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->fiche_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;
    }
    
}
