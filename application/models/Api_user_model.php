<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_user_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
        $this->tabel = 'users';
    }


    public function get_user_by_email($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get($this->tabel);

        return $query->row();
    }

    public function get_user_data($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get($this->tabel);
        $listData = $query->row();

        $data = [
            'id' => $listData->id,
            'username' => $listData->username,
            'email' => $listData->email,
            'role' => $listData->role,
            'slug' => $listData->slug,
            'logged_in' => true,
        ];

        return $data;
    }

    public function insert_data($data, $userId)
    {
        $insert = $this->db->where('id',$userId);
        $insert = $this->db->update($this->tabel,$data);
        if (!$insert){
            return false;
        }
        return true;
    }

}