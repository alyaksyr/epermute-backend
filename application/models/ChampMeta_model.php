<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ChampMeta_Model extends CI_Model
{
    protected $champ_meta_table = 'aqi_pp_champ_meta';

    public function champ($param)
    {
        $this->db->where('id',$param);
        $this->db->or_where('code',$param);
        $query = $this->db->get($this->champ_meta_table);
        return $query->row();        
    }

    public function champByCode($code)
    {
        $qry = $this->db->get_where($this->champ_meta_table,array('code'=>$code));
        return $qry->row();        
    }

    public function all_champ()
    {
        $query = $this->db->get($this->champ_meta_table);
        return $query->result();
    }

    public function create(array $data)
    {
        $this->db->insert($this->champ_meta_table,  $data);
        return $this->db->insert_id();
    }

    public function update(array $data, $id)
    {
        $query = $this->db->get_where($this->champ_meta_table,array('id'=>$id));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->champ_meta_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function delete($id)
    {
        $query = $this->db->get_where($this->champ_meta_table, array('id'=>$id));
        if ($this->db->affected_rows()>0) {
            $this->db->delete($this->champ_meta_table, array('id'=>$id));
            if ($this->db->affected_rows()>0) {
                return true;
            }
            return false;
        } 
        return false;

    }
    
}