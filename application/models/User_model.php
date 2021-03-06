<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_Model extends CI_Model
{
    protected $user_table = 'gp5das_users';

    public function user($id)
    {
        $qry= $this->db->get_where($this->user_table,array('id'=>$id));
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
        $query = $this->db->get_where($this->user_table,array('id'=>$data['id'], 'mobile'=>$data['mobile'],'token'=>$data['token']));
        if ($this->db->affected_rows()>0) {
        
            return $this->db->update($this->user_table,$data,['id'=>$query->row('id')]);
        } 
        return false;
    }
    
    public function user_login($login, $password)
    {
        $this->db->where('email',$login);
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
        $this->db->or_where('mobile',$login);
        $query = $this->db->get($this->user_table);
        if ($query->num_rows()) {
            return $query->row();
        } else {
            return FALSE;
        }
        
    }

}
