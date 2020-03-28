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

}