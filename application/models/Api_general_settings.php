<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_general_settings extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
        $this->tabel = 'general_settings';
    }


    public function getEmailVerivication()
    {
        $this->db->select('email_verification');
        $query = $this->db->get($this->tabel);
        $result = $query->row()->email_verification;
        return $result;
    }

    public function getValueOf($data)
    {
        $this->db->select($data);
        $query = $this->db->get($this->tabel);
        $result = $query->row()->$data;
        return $result;
    }

    public function getAll()
    {
        $this->db->where('id', 1);
        $query = $this->db->get($this->tabel)->row();
        return $query;
    }

}