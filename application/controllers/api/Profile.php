<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Profile extends REST_Controller{

	public function __construct(){
		parent::__construct();
		
		$this->return = array('status' => false, 'message' => 'Something wrong', 'data' => []);
		$this->load->helper('api_helper');
		$this->load->model("api_file_model");
	}

	public function listfavorite_post()
    {
    	$userSlug = $this->post('user_slug');
    	$slug = decode_slug($userSlug);
        $user = $this->auth_model->get_user_by_slug($slug);

        if (empty($user)) {
            $this->return['message'] = "User not found";
        }else {
        	$products = $this->product_model->get_user_favorited_products($user->id);
        	
    		$favoriteProduct = [];
        	if ($products) {
        		foreach ($products as $list) {
        			$favoriteList = listdataProduct($list);
        			$image = $this->api_file_model->get_image_by_product($list->id);
        			$favoriteList['image'] = generateImgProduct($image, 'image_small');
        			$favoriteProduct[] = $favoriteList;
        		}
        	}
        	$data["products"] = $favoriteProduct;
        	$data["total"] = count($favoriteProduct);

        	$this->return['status'] = true;
			$this->return['message'] = "Success";
			$this->return['data'] = $data;
        }

        $this->response($this->return);
    }

}