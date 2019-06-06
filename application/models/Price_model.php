<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Price_Model extends CI_Model
{
    protected $price_table = 'aqi_pp_price';

    public function valid_code($code){
        $qry = $this->db->get_where($this->price_table, array('code'=>$code));
        return ($qry->num_rows() <= 0)?'true':'false';              
    }

    public function get_price($str)
    {
        $this->db->select('*');
        $this->db->from($this->price_table);
        $this->db->where('id',$str);
        $this->db->or_where('code',$str);
        $qry = $this->db->get();
        return $qry->row();        
    }

    public function get_price_by_id_pays($str)
    {
        $this->db->select('*');
        $this->db->from($this->price_table);
        $this->db->where('id_pays',$str);
        $qry = $this->db->get();
        foreach($qry->result() as $row) {
            $price_data[]=$row;
        }    
        return $price_data;  
    }

    public function fetch_all_prices()
    {
        $query = $this->db->get($this->price_table);
        foreach ($query->result() as $row) {
            $price_data[]=$row;
        }
        return $price_data;
    }

    public function insert_price(array $data)
    {
        return $this->db->insert($this->price_table,  $data);
    }

    public function update_price($id)
    {
        $price = $this->get_price($id);
        $this->db->where('id', $id);
        return $this->db->update($this->price_table,$price);
    }
    
}