<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_product_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
        $this->tabel = 'products';
        $this->default_location_id = 0;
    }

    public function get_paginated_filtered_products_count($category_id, $subcategory_id, $third_category_id, $data)
    {
        $this->filter_products($category_id, $subcategory_id, $third_category_id, $data);
        $query = $this->db->get('products');
        return $query->num_rows();
    }
    
    public function get_paginated_filtered_products($category_id, $subcategory_id, $third_category_id, $per_page, $offset, $data)
    {
        $this->filter_products($category_id, $subcategory_id, $third_category_id, $data);
        $this->db->limit($per_page, $offset);
        $query = $this->db->get('products');
        return $query->result();
    }

    public function get_related_products($product)
    {
        $this->build_query();
        if ($product->third_category_id != 0) {
            $this->db->where('products.third_category_id', $product->third_category_id);
        } elseif ($product->subcategory_id != 0) {
            $this->db->where('products.subcategory_id', $product->subcategory_id);
        } else {
            $this->db->where('products.category_id', $product->category_id);
        }
        $this->db->where('products.id !=', $product->id);
        $this->db->limit(4);
        $this->db->order_by('products.created_at', 'DESC');
        $query = $this->db->get('products');
        return $query->result();
    }

    public function filter_products($category_id, $subcategory_id, $third_category_id, $data)
    {
        $category_id = clean_number($category_id);
        $subcategory_id = clean_number($subcategory_id);
        $third_category_id = clean_number($third_category_id);

        $country = clean_number($data['country']);
        $state = clean_number($data['state']);
        $city = clean_number($data['city']);
        $condition = remove_special_characters($data['condition']);
        $p_min = remove_special_characters($data['p_min']);
        $p_max = remove_special_characters($data['p_max']);
        $sort = remove_special_characters($data['sort']);
        $search = remove_special_characters(trim($data['search']));

        //check if custom filters selected
        $custom_filters = array();
        $session_custom_filters = get_sess_product_filters();
        $query_string_filters = get_filters_query_string_array();
        $array_queries = array();
        if (!empty($session_custom_filters)) {
            foreach ($session_custom_filters as $filter) {
                if (isset($query_string_filters[$filter->product_filter_key])) {
                    $item = new stdClass();
                    $item->product_filter_key = $filter->product_filter_key;
                    $item->product_filter_value = @$query_string_filters[$filter->product_filter_key];
                    array_push($custom_filters, $item);
                }
            }
        }
        if (!empty($custom_filters)) {
            foreach ($custom_filters as $filter) {
                if (!empty($filter)) {
                    $filter->product_filter_key = remove_special_characters($filter->product_filter_key);
                    $filter->product_filter_value = remove_special_characters($filter->product_filter_value);
                    $this->db->join('custom_fields_options', 'custom_fields_options.common_id = custom_fields_product.selected_option_common_id');
                    $this->db->select('product_id');
                    $this->db->where('custom_fields_product.product_filter_key', $filter->product_filter_key);
                    $this->db->where('custom_fields_options.field_option', $filter->product_filter_value);
                    $this->db->from('custom_fields_product');
                    $array_queries[] = $this->db->get_compiled_select();
                    $this->db->reset_query();
                }
            }
            $this->build_query();
            foreach ($array_queries as $query) {
                $this->db->where("products.id IN ($query)", NULL, FALSE);
            }
        } else {
            $this->build_query();
        }

        //add protuct filter options
        if (!empty($category_id)) {
            $this->db->where('products.category_id', $category_id);
            $this->db->order_by('products.is_promoted', 'DESC');
        }
        if (!empty($subcategory_id)) {
            $this->db->where('products.subcategory_id', $subcategory_id);
            $this->db->order_by('products.is_promoted', 'DESC');
        }
        if (!empty($third_category_id)) {
            $this->db->where('products.third_category_id', $third_category_id);
            $this->db->order_by('products.is_promoted', 'DESC');
        }
        if (!empty($country)) {
            $this->db->where('products.country_id', $country);
        }
        if (!empty($state)) {
            $this->db->where('products.state_id', $state);
        }
        if (!empty($city)) {
            $this->db->where('products.city_id', $city);
        }
        if (!empty($condition)) {
            $this->db->where('products.product_condition', $condition);
        }
        if ($p_min != "") {
            $this->db->where('products.price >=', intval($p_min * 100));
        }
        if ($p_max != "") {
            $this->db->where('products.price <=', intval($p_max * 100));
        }
        if ($search != "") {
            $this->db->group_start();
            $this->db->like('products.title', $search);
            $this->db->or_like('products.description', $search);
            $this->db->group_end();
            $this->db->order_by('products.is_promoted', 'DESC');
        }
        //sort products
        if (!empty($sort) && $sort == "lowest_price") {
            $this->db->order_by('products.price');
        } elseif (!empty($sort) && $sort == "highest_price") {
            $this->db->order_by('products.price', 'DESC');
        } else {
            $this->db->order_by('products.created_at', 'DESC');
        }
    }

    public function build_query()
    {
        $this->db->join('users', 'products.user_id = users.id');
        $this->db->select('products.*, users.username as user_username, users.shop_name as shop_name, users.role as user_role, users.slug as user_slug');
        $this->db->where('users.banned', 0);
        $this->db->where('users.role !=', 'member');
        $this->db->where('products.status', 1);
        $this->db->where('products.visibility', 1);
        $this->db->where('products.is_draft', 0);
        $this->db->where('products.is_deleted', 0);

        //default location
        if ($this->default_location_id != 0) {
            $this->db->where('products.country_id', $this->default_location_id);
        }
    }

    public function add_remove_favorites($data)
    {
        $productId = clean_number($data['product_id']);
        if ($data['user_id']) {
            if ($this->is_product_in_favorites($data)) {
                $this->db->where('user_id', $data['user_id']);
                $this->db->where('product_id', $productId);
                $this->db->delete('favorites');
            } else {
                $data = array(
                    'user_id' => $data['user_id'],
                    'product_id' => $productId
                );
                $this->db->insert('favorites', $data);
            }
        }
    }

    public function is_product_in_favorites($data)
    {
        $productId = clean_number($data['product_id']);
        if ($data['user_id']) {
            $this->db->where('user_id', $data['user_id']);
            $this->db->where('product_id', $productId);
            $query = $this->db->get('favorites');
            if (!empty($query->row())) {
                return true;
            }
        }
        return false;
    }

}