<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_file_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
        $this->tabel = 'images';
    }


    public function get_image_by_product($productId)
	{
		$this->db->where('product_id', $productId);
		$this->db->order_by('images.is_main', 'DESC');
		$query = $this->db->get($this->tabel);
		$row = $query->row();

		return $row;
	}


	public function get_product_images($productId)
	{
		$this->db->where('product_id', $productId);
		$this->db->order_by('images.is_main', 'DESC');
		$query = $this->db->get('images');
		$rows = $query->result();
		
		return $rows;
	}

}