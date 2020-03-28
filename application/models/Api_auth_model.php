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

    //login with google
    public function regis_with_google($g_user)
    {
        if (empty($g_user['given_name'])) {
            $g_user['given_name'] = "user-" . uniqid();
        }
        $username = $this->generate_uniqe_username($g_user['given_name']);
        $slug = $this->generate_uniqe_slug($username);
        //add user to database
        $data = array(
            'google_id' => $g_user['id'],
            'email' => $g_user['email'],
            'device_id' => $g_user['device_id'],
            'email_status' => 1,
            'token' => generate_unique_id(),
            'username' => $username,
            'slug' => $slug,
            'role' => "vendor",
            'avatar' => $g_user['photo_url'],
            'user_type' => "google",
            'created_at' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('users', $data);
        $user = $this->api_user_model->get_user_data($g_user['email']);

        return $user;
    }

    //generate uniqe username
    public function generate_uniqe_username($username)
    {
        $new_username = $username;
        if (!empty($this->get_user_by_username($new_username))) {
            $new_username = $username . " 1";
            if (!empty($this->get_user_by_username($new_username))) {
                $new_username = $username . " 2";
                if (!empty($this->get_user_by_username($new_username))) {
                    $new_username = $username . " 3";
                    if (!empty($this->get_user_by_username($new_username))) {
                        $new_username = $username . "-" . uniqid();
                    }
                }
            }
        }
        return $new_username;
    }

    //generate uniqe slug
    public function generate_uniqe_slug($username)
    {
        $slug = str_slug($username);
        if (!empty($this->get_user_by_slug($slug))) {
            $slug = str_slug($username . "-1");
            if (!empty($this->get_user_by_slug($slug))) {
                $slug = str_slug($username . "-2");
                if (!empty($this->get_user_by_slug($slug))) {
                    $slug = str_slug($username . "-3");
                    if (!empty($this->get_user_by_slug($slug))) {
                        $slug = str_slug($username . "-" . uniqid());
                    }
                }
            }
        }
        return $slug;
    }

    public function get_user_by_username($username)
    {
        $username = remove_special_characters($username);
        $this->db->where('username', $username);
        $query = $this->db->get('users');
        return $query->row();
    }

    public function get_user_by_slug($slug)
    {
        $slug = clean_slug($slug);
        $this->db->where('slug', $slug);
        $query = $this->db->get('users');
        return $query->row();
    }
}