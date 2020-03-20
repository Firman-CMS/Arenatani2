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

    public function add_product($post)
    {
        $data = array(
            'title' => $post['title'],
            'product_type' => "physical",
            'listing_type' => "ordinary_listing",
            'category_id' => $post['category_id'],
            'subcategory_id' => "",
            'third_category_id' => "",
            'price' => 0,
            'currency' => "",
            'description' => $post['description'],
            'product_condition' => "",
            'country_id' => 0,
            'state_id' => 0,
            'city_id' => 0,
            'address' => "",
            'zip_code' => "",
            'user_id' => $post['user_id'],
            'status' => 0,
            'is_promoted' => 0,
            'promote_start_date' => date('Y-m-d H:i:s'),
            'promote_end_date' => date('Y-m-d H:i:s'),
            'promote_plan' => "none",
            'promote_day' => 0,
            'visibility' => 1,
            'rating' => 0,
            'hit' => 0,
            'demo_url' => "",
            'external_link' => "",
            'files_included' => "",
            'quantity' => 1,
            'shipping_time' => "",
            'shipping_cost_type' => "",
            'shipping_cost' => 0,
            'is_sold' => 0,
            'is_deleted' => 0,
            'is_draft' => 1,
            'created_at' => date('Y-m-d H:i:s')
        );

        $data["slug"] = str_slug($data["title"]);

        if (empty($data["subcategory_id"])) {
            $data["subcategory_id"] = 0;
        }
        if (empty($data["third_category_id"])) {
            $data["third_category_id"] = 0;
        }
        if (empty($data["country_id"])) {
            $data["country_id"] = 0;
        }
        if ($this->api_general_settings->getValueOf('approve_before_publishing') == 0) {
            $data["status"] = 1;
        }

        if ($this->db->insert('products', $data)) {
            return $this->db->insert_id();
        }else {
            return false;
        }
    }

    //edit product details
    public function edit_product_details($post)
    {
        $id = clean_number($post['product_id']);
        $product = $this->get_product_by_id($id);
        $data = array(
            'price' => $post['price'],
            'currency' => 'IDR',
            'product_condition' => $post['product_condition'],
            'country_id' => $post['country_id'],
            'state_id' => $post['state_id'],
            'city_id' => $post['city_id'],
            'address' => $post['address'],
            'zip_code' => $post['zip_code'],
            'demo_url' => "",
            'external_link' => "",
            'files_included' => "",
            'quantity' => $post['quantity'],
            'shipping_time' => $post['shipping_time'],
            'shipping_cost_type' => $post['shipping_cost_type'],
            'is_draft' => 0
        );

        $data["price"] = price_database_format($data["price"]);
        if (empty($data["price"])) {
            $data["price"] = 0;
        }
        if (empty($data["product_condition"])) {
            $data["product_condition"] = "";
        }
        if (empty($data["country_id"])) {
            $data["country_id"] = 0;
        }
        if (empty($data["state_id"])) {
            $data["state_id"] = 0;
        }
        if (empty($data["city_id"])) {
            $data["city_id"] = 0;
        }
        if (empty($data["address"])) {
            $data["address"] = "";
        }
        if (empty($data["zip_code"])) {
            $data["zip_code"] = "";
        }
        if (empty($data["external_link"])) {
            $data["external_link"] = "";
        }
        if (empty($data["quantity"])) {
            $data["quantity"] = 1;
        }

        if ($this->settings_model->is_shipping_option_require_cost($data["shipping_cost_type"]) == 1) {
            $data["shipping_cost"] = $this->input->post('shipping_cost', true);
            $data["shipping_cost"] = price_database_format($data["shipping_cost"]);
        } else {
            $data["shipping_cost"] = 0;
        }

        $this->db->where('id', $id);
        return $this->db->update('products', $data);
    }

    //edit product
    public function edit_product($post)
    {
        $id = clean_number($post['product_id']);
        $data = array(
            'title' => $post['title'],
            'product_type' => 'physical',
            'listing_type' => 'ordinary_listing',
            'category_id' => $post['category_id'],
            'subcategory_id' => "",
            'third_category_id' => "",
            'description' => $post['description']
        );
        $data["slug"] = str_slug($data["title"]);
        if (empty($data["subcategory_id"])) {
            $data["subcategory_id"] = 0;
        }
        if (empty($data["third_category_id"])) {
            $data["third_category_id"] = 0;
        }
        $is_sold = $post['status_sold'];
        if ($is_sold == "active") {
            $data["is_sold"] = 0;
        } elseif ($is_sold == "sold") {
            $data["is_sold"] = 1;
        }
        if (is_admin()) {
            $data["visibility"] = $this->input->post('visibility', true);
        }
        $this->db->where('id', $id);
        return $this->db->update('products', $data);
    }

    //save as draft
    public function saveasdraft($post)
    {
        $id = clean_number($post['product_id']);
        $product = $this->get_product_by_id($id);
        $data = array(
            'price' => $post['price'],
            'currency' => 'IDR',
            'product_condition' => $post['product_condition'],
            'country_id' => $post['country_id'],
            'state_id' => $post['state_id'],
            'city_id' => $post['city_id'],
            'address' => $post['address'],
            'zip_code' => $post['zip_code'],
            'demo_url' => "",
            'external_link' => "",
            'files_included' => "",
            'quantity' => $post['quantity'],
            'shipping_time' => $post['shipping_time'],
            'shipping_cost_type' => $post['shipping_cost_type'],
            'is_draft' => 1
        );

        $data["price"] = price_database_format($data["price"]);
        if (empty($data["price"])) {
            $data["price"] = 0;
        }
        if (empty($data["product_condition"])) {
            $data["product_condition"] = "";
        }
        if (empty($data["country_id"])) {
            $data["country_id"] = 0;
        }
        if (empty($data["state_id"])) {
            $data["state_id"] = 0;
        }
        if (empty($data["city_id"])) {
            $data["city_id"] = 0;
        }
        if (empty($data["address"])) {
            $data["address"] = "";
        }
        if (empty($data["zip_code"])) {
            $data["zip_code"] = "";
        }
        if (empty($data["external_link"])) {
            $data["external_link"] = "";
        }
        if (empty($data["quantity"])) {
            $data["quantity"] = 1;
        }

        if ($this->settings_model->is_shipping_option_require_cost($data["shipping_cost_type"]) == 1) {
            $data["shipping_cost"] = $this->input->post('shipping_cost', true);
            $data["shipping_cost"] = price_database_format($data["shipping_cost"]);
        } else {
            $data["shipping_cost"] = 0;
        }

        $this->db->where('id', $id);
        return $this->db->update('products', $data);
    }

    public function get_product_by_id($id)
    {
        $id = clean_number($id);
        $this->db->where('id', $id);
        $query = $this->db->get('products');
        return $query->row();
    }

    //update custom fields
    public function update_product_custom_fields($post)
    {
        $product_id = clean_number($post['product_id']);
        $product = $this->get_product_by_id($product_id);
        $sitelang = api_lang_helper()->id;
        if (!empty($product)) {
            $custom_fields = $this->api_field_model->generate_custom_fields_array($product->category_id, $product->subcategory_id, $product->third_category_id, $product_id, $sitelang);
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_field) {
                    //check field values
                    $field_values = $this->field_model->get_product_custom_field_values($custom_field->id, $product_id);
                    $input_value = $post['satuan'];

                    //update custom field values
                    if (!empty($field_values)) {
                        if ($custom_field->field_type == 'checkbox') {
                            $this->update_checkbox_selected_values($custom_field, $field_values, $input_value, $product_id);
                        } else {
                            $field_value_id = 0;
                            if (isset($field_values[0]->id)) {
                                $field_value_id = $field_values[0]->id;
                            }
                            if (!empty($field_value_id)) {
                                if ($custom_field->field_type == 'radio_button' || $custom_field->field_type == 'dropdown') {
                                    $data = array(
                                        'selected_option_common_id' => $input_value
                                    );
                                } else {
                                    $data = array(
                                        'field_value' => $input_value,
                                    );
                                }
                                $this->db->where('id', $field_value_id);
                                $this->db->update('custom_fields_product', $data);
                            }
                        }
                    } else {
                        //add custom field values
                        if (!empty($input_value)) {
                            if ($custom_field->field_type == 'checkbox') {
                                foreach ($input_value as $key => $value) {
                                    $data = array(
                                        'field_id' => $custom_field->id,
                                        'product_id' => $product_id,
                                        'product_filter_key' => $custom_field->product_filter_key
                                    );
                                    $data['field_value'] = '';
                                    $data['selected_option_common_id'] = $value;
                                    $this->db->insert('custom_fields_product', $data);
                                }
                            } else {
                                $data = array(
                                    'field_id' => $custom_field->id,
                                    'product_id' => $product_id,
                                    'product_filter_key' => $custom_field->product_filter_key,
                                );
                                if ($custom_field->field_type == 'radio_button' || $custom_field->field_type == 'dropdown') {
                                    $data['field_value'] = '';
                                    $data['selected_option_common_id'] = $input_value;
                                } else {
                                    $data['field_value'] = $input_value;
                                    $data['selected_option_common_id'] = '';
                                }
                                $this->db->insert('custom_fields_product', $data);
                            }
                        }
                    }
                }
            }
        }
    }

    public function set_product_as_sold($product_id)
    {
        $product_id = clean_number($product_id);
        $product = $this->get_product_by_id($product_id);
        if (!empty($product)) {
            if ($product->is_sold == 1) {
                $data = array(
                    'is_sold' => 0
                );
            } else {
                $data = array(
                    'is_sold' => 1
                );
            }
            $this->db->where('id', $product_id);
            return $this->db->update('products', $data);
        }
        return false;
    }

    public function delete_product($product_id)
    {
        $product_id = clean_number($product_id);
        $data = array(
            'is_deleted' => 1
        );
        $this->db->where('id', $product_id);
        return $this->db->update('products', $data);
    }

}