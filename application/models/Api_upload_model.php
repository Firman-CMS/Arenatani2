<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Gumlet\ImageResize;
use \Gumlet\ImageResizeException;

class Api_upload_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
        // $this->tabel = 'images';
        $this->load->model("api_general_settings");
    }


    public function resizeImage($productId, $path)
    {
    	$data = array(
			'product_id' => $productId,
			'image_default' => $this->api_upload_model->product_image_upload($path, "880"),
			'image_big' => $this->api_upload_model->product_image_upload($path, "1920"),
			'image_small' => $this->api_upload_model->product_image_upload($path, "300"),
			'is_main' => 0,
			'storage' => "local"
		);

		$this->db->insert('images', $data);
		// $data = array(
		// 	'image_id' => $this->db->insert_id()
		// );
		// echo json_encode($data);
    }
    public function product_image_upload($path, $size)
	{
		try {
			$image = new ImageResize($path);
			$image->quality_jpg = 85;
			$image->resizeToHeight($size);
			$new_name = 'img_x'.$size.'_' . generate_unique_id() . '.jpg';
			$new_path = 'uploads/images/' . $new_name;
			$image->save(FCPATH . $new_path, IMAGETYPE_JPEG);
			
			return $new_name;
		} catch (ImageResizeException $e) {
			return null;
		}
	}

	//delete temp image
	public function delete_temp_image($path)
	{
		if (file_exists($path)) {
			@unlink($path);
		}
	}

	//avatar image upload
	public function avatar_upload($path)
	{
		try {
			$image = new ImageResize($path);
			$image->quality_jpg = 85;
			$image->crop(240, 240, true);
			$new_path = 'uploads/profile/avatar_' . generate_unique_id() . '.jpg';
			$image->save(FCPATH . $new_path, IMAGETYPE_JPEG);
			return $new_path;
		} catch (ImageResizeException $e) {
			return null;
		}
	}

	
}