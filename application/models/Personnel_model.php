<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Personnel_Model extends CI_Model
{
    protected $personnel_table = 'gp5das_personnel';
    protected $select = '
        user_pers id,
        matricule_pers matricule,
        nom_pers,
        nom_jeune_fille_pers nomjeunefille,
        prenoms_pers,
        date_nais_pers datedenaissance,
        lieu_nais_pers lieudenaissance,
        marie_pers situationmatrimoniale,
        sante_pers etatdesante,
        iepp_pers inspection,
        localite_pers adresse,
        emploi_pers emploi,
        fonction_pers fonction,
        service_pers ecole,
        classe_tenue_pers classe
        ';

    public function personnel($id)
    {
        $qry= $this->db->get_where($this->personnel_table,array('user_pers'=>$id));
        return $qry->row();       
    }

    public function personnel_matricule($matricule)
    {
        $qry= $this->db->get_where($this->personnel_table,array('matricule_pers'=>$matricule));
        return $qry->row();       
    }

    public function personnel_information($param)
    {
        $this->db->select($this->select);
        $this->db->from('gp5das_personnel u');
        $this->db->where('user_pers',$param);
        $this->db->or_where('matricule_pers',$param);
        $qry = $this->db->get();
        return $qry->row();        
    }
    
    public function fetch_all_personnels()
    {
        $query = $this->db->get($this->personnel_table);
        foreach ($query->result() as $row) {
            $personnel_data[]=$row;
        }
        return $personnel_data;
    }
    
    public function insert_personnel(array $data)
    {
        $this->db->insert($this->personnel_table,  $data);
        return $this->db->insert_id();
    }

    public function update_personnel(array $data)
    {
        $query = $this->db->get_where($this->personnel_table,array('user_pers'=>$data['user_pers'], 'matricule_pers'=>$data['matricule_pers']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->personnel_table,$data,['user_pers'=>$query->row('user_pers')]);
        } 
        return false;
    }

}
