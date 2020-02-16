<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_auth_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
        $this->load->model("api_user_model");
    }


    public function login($post)
    {
        $this->load->library('bcrypt');

        $user = $this->api_user_model->get_user_by_email($post['email']);

        if ($user) {
            if (!$this->bcrypt->check_password($post['password'], $user->password)) {
                return false;
            }
            if ($user->banned == 1) {
                return false;
            }
            
            $dataInsert = ['device_id' => $post['device_id']];
        	if (!$this->api_user_model->insert_data($dataInsert, $user->id)){
        		return false;
        	}

        	return true;
        }else{
        	return false;
        }
    }
}