<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_Model extends CI_Model
{
    protected $user_table = 'gp5das_user';

    public function user($id)
    {
        $qry= $this->db->get_where($this->user_table,array('id'=>$id));
        return $qry->row();       
    }

    public function user_matricule($matricule)
    {
        $qry= $this->db->get_where($this->user_table,array('matricule'=>$matricule));
        return $qry->row();       
    }

    public function user_detail($id)
    {
        $this->db->select('mobile,nom,nom_jeune_fille,prenom,email,matricule');
        $this->db->from($this->user_table);
        $this->db->where('id',$id);
        $qry = $this->db->get();
        return $qry->row();        
    }

    public function user_information($param)
    {
        $this->db->select('u.id,matricule,u.nom,nom_jeune_fille nomjeunefille,prenoms,date_nais datedenaissance,lieu_nais lieudenaissance,marie situationmatrimoniale,sante etatdesante,mobile,u.email,id_iepp inspection,localite,emplois,fonction,service ecole,classe_tenue classe, role');
        $this->db->from('gp5das_user u');
        $this->db->where('u.id',$param);
        $this->db->or_where('u.mobile',$param);
        $this->db->or_where('u.matricule',$param);
        $this->db->or_where('u.email',$param);
        $qry = $this->db->get();
        return $qry->row();        
    }
    
    public function fetch_all_users()
    {
        $query = $this->db->get($this->user_table);
        foreach ($query->result() as $row) {
            $user_data[]=$row;
        }
        return $user_data;
    }
    
    public function insert_user(array $data)
    {
        $this->db->insert($this->user_table,  $data);
        return $this->db->insert_id();
    }

    public function update_user(array $data)
    {
        $query = $this->db->get_where($this->user_table,array('id'=>$data['id'], 'matricule'=>$data['matricule']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->user_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function update_user_by_email($user, array $data)
    {
        $this->db->where('email',$user);
        $this->db->or_where('mobile',$user);
        $query = $this->db->get($this->user_table);
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->user_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }

    public function update_set_password_user(array $data)
    {
        $query = $this->db->get_where($this->user_table,array('id'=>$data['id'], 'matricule'=>$data['matricule'],'token'=>$data['token']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->user_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }
    
    public function user_login($login, $password)
    {
        $this->db->where('matricule',$login);
        $this->db->or_where('email',$login);
        $this->db->or_where('mobile',$login);
        $query = $this->db->get($this->user_table);
        if ($query->num_rows()) {
            $hash = $query->row('password');
            if(password_verify($password,$hash)){
                return $query->row();
            }else{
                return FALSE;
            } 
        } else {
            return FALSE;
        }
        
    }

    public function user_check_email_or_mobile($user)
    {
        $this->db->where('email',$user);
        $this->db->or_where('mobile',$user);
        $query = $this->db->get($this->user_table);
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return FALSE;
        }
        
    }

    public function user_check($login, $code)
    {
        $this->db->where('id',$login);
        $this->db->or_where('email',$login);
        $this->db->or_where('matricule',$login);
        $this->db->or_where('mobile',$login);
        $query = $this->db->get($this->user_table);
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return FALSE;
        }
        
    }

}
