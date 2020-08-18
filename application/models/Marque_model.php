<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Marque_Model extends CI_Model
{
    protected $marque_table = 'aqi_pp_marque';

    public function marque($id)
    {
        $qry = $this->db->get_where($this->marque_table,array('marque_id'=>$id));
        return $qry->row();
    }

    public function all_marque()
    {
        $query = $this->db->get($this->marque_table);
        return $query->result_array();
    }

    public function create(array $data)
    {
        $this->db->insert($this->marque_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data)
    {
        $query = $this->db->get_where($this->marque_table,array('marque_id'=>$data['marque_id']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->marque_table,$data,['marque_id'=>$query->row('marque_id')]);
        } 
        return false;
    }

    public function delete(array $data)
    {
        $query = $this->db->get_where($this->marque_table, $data);
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->marque_table, $data);
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;
    }

    /**
     * Get Mrque libelle
     * @param: id
     */
    public function marque_libelle($param='')
    {
        $this->db->select('marque_id id,marque_libelle name, marque_slug slug, marque_image image');
        $this->db->from($this->marque_table);
        if ($param ==='') {
            return $this->db->get()->result();
        } else {
            $this->db->where('marque_id',$param);
            $this->db->or_where('marque_slug',$param);
            return $this->db->get()->row();
        }
               
    }

    public function marque_modele($id){
        $this->db->select('modele_id,modele_code,modele_libelle,modele_infos,ma.*');
        $this->db->from('aqi_pp_modele as mo');
        $this->db->join('aqi_pp_marque as ma','mo.id_marque = ma.marque_id','left');
        $this->db->where(array('mo.modele_id'=>$id));
        return $this->db->get()->row();
    }
}