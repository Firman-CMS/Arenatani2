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

    //add review
    public function add_review($post)
    {
        $data = array(
            'product_id' => $post['product_id'],
            'user_id' => $post['user_id'],
            'rating' => $post['rating'],
            'review' => $post['review'],
            'created_at' => date("Y-m-d H:i:s")
        );

        $this->db->insert('reviews', $data);

        //update product rating
        $this->update_product_rating($data['product_id']);
    }

    //update product rating
    public function update_product_rating($product_id)
    {
        $product_id = clean_number($product_id);
        $reviews = $this->get_reviews($product_id);
        $data = array();
        if (!empty($reviews)) {
            $count = count($reviews);
            $total = 0;
            foreach ($reviews as $review) {
                $total += $review->rating;
            }
            $data['rating'] = round($total / $count);
        } else {
            $data['rating'] = 0;
        }
        $this->db->where('id', $product_id);
        $this->db->update('products', $data);
    }

    public function get_reviews($product_id)
    {
        $product_id = clean_number($product_id);
        $this->db->join('users', 'users.id = reviews.user_id');
        $this->db->select('reviews.*, users.username as user_username');
        $this->db->where('reviews.product_id', $product_id);
        $this->db->order_by('reviews.created_at', 'DESC');
        $query = $this->db->get('reviews');
        return $query->result();
    }

}