<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_review_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
        $this->load->model("api_user_model");
        $this->load->model("auth_model");
        $this->load->model("api_email_model");
        $this->load->model("api_general_settings");
    }

    //get review count
    public function get_review_user_count($userId)
    {
        $this->db->where('reviews.user_id', $userId);
        $query = $this->db->get('reviews');
        return $query->num_rows();
    }

    //get reviews
    public function get_reviews_user($userId)
    {
        $this->db->select('product_id');
        $this->db->where('user_id', $userId);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('reviews');
        return $query->result();
    }

}