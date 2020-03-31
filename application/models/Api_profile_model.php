<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_profile_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
    }

    //follow user
    public function follow_unfollow_user($data)
    {
        $follow = $this->get_follow($data["following_id"], $data["follower_id"]);
        if (empty($follow)) {
            //add follower
            $this->db->insert('followers', $data);
        } else {
            $this->db->where('id', $follow->id);
            $this->db->delete('followers');
        }
    }

    public function get_follow($following_id, $follower_id)
    {
        $following_id = clean_number($following_id);
        $follower_id = clean_number($follower_id);
        $this->db->where('following_id', $following_id);
        $this->db->where('follower_id', $follower_id);
        $query = $this->db->get('followers');
        return $query->row();
    }

    public function update_contact_informations($data)
    {
        $userId = $data['user_id'];
        unset($data['user_id']);

        if (empty($data['show_email'])) {
            $data['show_email'] = 0;
        }
        if (empty($data['show_phone'])) {
            $data['show_phone'] = 0;
        }
        if (empty($data['show_location'])) {
            $data['show_location'] = 0;
        }

        $this->db->where('id', $userId);
        return $this->db->update('users', $data);
    }

    public function update_profile($data)
    {
        $userId = $data['user_id'];
        unset($data['user_id']);

        $this->db->where('id', $userId);
        return $this->db->update('users', $data);
    }

    //check email updated
    public function check_email_updated($email, $user_id)
    {
        $user_id = clean_number($user_id);
        if ($this->api_general_settings->getValueOf('email_verification') == 1) {
            $user = $this->auth_model->get_user($user_id);
            if (!empty($user)) {
                if ($email != $user->email) {
                    $this->load->model("email_model");
                    $this->email_model->send_email_activation($user->id);
                    $data = array(
                        'email_status' => 0
                    );

                    $this->db->where('id', $user->id);
                    return $this->db->update('users', $data);
                }
            }
        }

        return false;
    }

    //change password
    public function change_password($data)
    {
        $this->load->library('bcrypt');

        $user = $this->auth_model->get_user($data['user_id']);

        if ($this->bcrypt->check_password($data['old_password'], $user->password)) {

            $pass = [
                'password' => $this->bcrypt->hash_password($data['password'])
            ];

            $this->db->where('id', $user->id);
            return $this->db->update('users', $pass);
        } else {
            return false;
        }
    }

}