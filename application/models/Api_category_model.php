<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_category_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
        $this->tabela = 'categories';
    }


    //get parent categories
    public function get_parent_categories($sitelangId)
    {
        $this->db->join('categories_lang', 'categories_lang.category_id = categories.id');
        $this->db->select('categories.*, categories_lang.lang_id as lang_id, categories_lang.name as name');
        $this->db->where('categories_lang.lang_id', $sitelangId);
        $this->db->where('category_level', 1);
        $this->db->where('categories.visibility', 1);
        $this->db->order_by('category_order');
        $query = $this->db->get($this->tabela);
        return $query->result();
    }

    public function get_category_joined($id, $sitelangId)
    {
        $id = clean_number($id);
        $this->db->join('categories_lang', 'categories_lang.category_id = categories.id');
        $this->db->select('categories.*, categories_lang.lang_id as lang_id, categories_lang.name as name');
        $this->db->where('categories_lang.lang_id', $sitelangId);
        $this->db->where('categories.id', $id);
        $this->db->where('categories.visibility', 1);
        $query = $this->db->get('categories');
        return $query->row();
    }

}
