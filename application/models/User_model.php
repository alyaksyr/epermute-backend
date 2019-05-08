<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_Model extends CI_Model
{
    protected $user_table = 'aqi_pp_users';

    public function valid_email($email){
        $qry = $this->db->get_where($this->user_table, array('email'=>$email));
        return ($qry->num_rows() <= 0)?'true':'false';              
    }

    public function valid_login($login){
        $qry = $this->db->get_where($this->user_table, array('login'=>$login));
        return ($qry->num_rows() <= 0)?'true':'false';              
    }

    public function valid_mobile($mobile){
        $qry = $this->db->get_where($this->user_table, array('mobile'=>$mobile));
        return ($qry->num_rows() <= 0)?'true':'false';              
    }

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

    public function force_login($username,$password)
	{	   
	   $username = trim(strip_tags($username));
       $password = trim(strip_tags($password));
       $SQL = "SELECT * 
                  FROM aqi_pp_users
                 WHERE login = ?
                   AND password = ?";                   
       $query = $this->db->query($SQL, array($username,md5($password)));              
       if(!$query) return false;
       $user = $query->row_array();
       if(is_null($user)) return false; 
       if(!$user){
        return false;
       }else{
        $this->session->set_userdata('hash', 'some_value');
        $this->session->set_userdata('user',$user);
        return true;        
       }                    
    } 
    
    public function insert_user(array $data)
    {
        return $this->db->insert($this->user_table,  $data);
    }

    public function fetch_all_users()
    {
        $query = $this->db->get($this->user_table);
        foreach ($query->result() as $row) {
            $user_data[]=$row;
        }
        return $user_data;
    }
}
