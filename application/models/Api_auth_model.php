<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_auth_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
        $this->load->model("api_user_model");
        $this->load->model("auth_model");
        $this->load->model("api_email_model");
        $this->load->model("api_general_settings");
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

    public function register($datas)
    {
        $this->load->library('bcrypt');

        if (!$datas['username'] || !$datas['email']) {
        	return false;
        }

        $data['username'] = $datas['username'];
        $data['email'] = $datas['email'];
        $data['password'] = $this->bcrypt->hash_password($datas['password']);
        $data['user_type'] = "registered";
        $data["slug"] = $this->auth_model->generate_uniqe_slug($datas["username"]);
        $data['banned'] = 0;
        $data['role'] = "vendor";
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['token'] = generate_token();

        if ($this->db->insert('users', $data)) {
            $last_id = $this->db->insert_id();
            if ($this->api_general_settings->getValueOf('email_verification') == 1) {
                $data['email_status'] = 0;
                $this->api_email_model->send_email_activation($last_id);
            } else {
                $data['email_status'] = 1;
            }
            return $this->auth_model->get_user($last_id);
        } else {
            return false;
        }
    }
}