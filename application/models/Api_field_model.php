<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_field_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
    }


    public function generate_custom_fields_array($category_id, $subcategory_id, $third_category_id, $product_id, $selected_lang)
    {
        $category_id = clean_number($category_id);
        $subcategory_id = clean_number($subcategory_id);
        $third_category_id = clean_number($third_category_id);
        $product_id = clean_number($product_id);

        $array = array();
        $custom_fields = $this->get_category_fields($category_id, $subcategory_id, $third_category_id, $selected_lang);
        foreach ($custom_fields as $custom_field) {
            $data = new stdClass();
            $data->id = $custom_field->id;
            $data->field_type = $custom_field->field_type;
            $data->name = $custom_field->name;
            $data->is_required = $custom_field->is_required;
            $data->category_id = $custom_field->category_id;
            $data->row_width = $custom_field->row_width;
            $data->product_filter_key = $custom_field->product_filter_key;
            $data->field_value = "";
            $data->field_common_ids = array();

            $field_values = $this->get_product_custom_field_values($custom_field->id, $product_id);
            if (!empty($field_values)) {
                foreach ($field_values as $field_value) {
                    if (!empty($field_value->field_value)) {
                        $data->field_value = $field_value->field_value;
                    }
                    $data->field_common_ids[] = $field_value->selected_option_common_id;
                }
            }
            array_push($array, $data);
        }
        return $array;
    }

    //get category fields
    public function get_category_fields($category_id, $subcategory_id, $third_category_id, $selected_lang)
    {
        $category_id = clean_number($category_id);
        $subcategory_id = clean_number($subcategory_id);
        $third_category_id = clean_number($third_category_id);
        $this->db->join('custom_fields_lang', 'custom_fields_lang.field_id = custom_fields.id');
        $this->db->join('custom_fields_category', 'custom_fields_category.field_id = custom_fields.id');
        $this->db->select('custom_fields.*, custom_fields_lang.lang_id as lang_id, custom_fields_lang.name as name, custom_fields_category.category_id as category_id');
        $this->db->where('custom_fields_lang.lang_id', $selected_lang);
        $this->db->where('custom_fields.status', 1);
        $this->db->group_start();
        $this->db->where('custom_fields_category.category_id', $category_id);
        if (!empty($subcategory_id)) {
            $this->db->or_where('custom_fields_category.category_id', $subcategory_id);
        }
        if (!empty($third_category_id)) {
            $this->db->or_where('custom_fields_category.category_id', $third_category_id);
        }
        $this->db->group_end();
        $this->db->order_by('custom_fields.field_order');
        $query = $this->db->get('custom_fields');
        return $query->result();
    }

    public function get_product_custom_field_values($field_id, $product_id)
    {
        $field_id = clean_number($field_id);
        $product_id = clean_number($product_id);
        $this->db->where('field_id', $field_id);
        $this->db->where('product_id', $product_id);
        $query = $this->db->get('custom_fields_product');
        return $query->result();
    }

    //get field options
    public function get_field_options($field_id,$lang_id)
    {
        $field_id = clean_number($field_id);
        $this->db->where('lang_id', $lang_id);
        $this->db->where('field_id', $field_id);
        $query = $this->db->get('custom_fields_options');
        return $query->result();
    }

}