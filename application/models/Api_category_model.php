<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_category_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
        // $this->load->model('api_general_setting');
        $this->tabela = 'categories';
    }


    //get parent categories
    public function get_parent_categories($sitelang)
    {
        $this->db->join('categories_lang', 'categories_lang.category_id = categories.id');
        $this->db->select('categories.*, categories_lang.lang_id as lang_id, categories_lang.name as name');
        $this->db->where('categories_lang.lang_id', $sitelang);
        $this->db->where('category_level', 1);
        $this->db->where('categories.visibility', 1);
        $this->db->order_by('category_order');
        $query = $this->db->get($this->tabela);
        return $query->result();
    }

    public function getSiteLang()
    {
        return $this->api_general_settings->getValueOf('site_lang');
    }

}
