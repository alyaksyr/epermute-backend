<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_Model extends CI_Model
{
    protected $user_table = 'aqi_pp_users';

    public function get_user_id($str)
    {
        $this->db->select('id');
        $this->db->from($this->user_table);
        $this->db->where('login',$str);
        $this->db->or_where('code',$str);
        $qry = $this->db->get();
        return ($qry->row())?$qry->row()->id:false;        
    }

    public function get_user($str)
    {
        $this->db->select('*');
        $this->db->from($this->user_table);
        $this->db->where('id',$str);
        $this->db->or_where('code',$str);
        $this->db->or_where('login',$str);
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
    
    public function user_login($login, $password)
    {
        $this->db->where('login',$login);
        $this->db->where('email',$login);
        $this->db->or_where('mobile',$login);
        $query = $this->db->get($this->user_table);
        if ($query->num_rows()) {
            $user_pass = $query->row('password');
            if($password === $user_pass){
                return $query->row();
            }else{
                return FALSE;
            }
        } else {
            return FALSE;
        }
        
    }

}
