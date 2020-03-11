<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'third_party/image-resize/ImageResize.php';
require APPPATH . 'third_party/image-resize/ImageResizeException.php';

class Sell extends REST_Controller{

	public function __construct(){
		parent::__construct();
		
		$this->load->model("api_file_model");
		$this->load->model("api_upload_model");
		$this->load->model("api_product_model");
		$this->load->model("api_field_model");
		$this->load->helper('api_helper');
		$this->load->helper('custom_helper');
		$this->return = array('status' => false, 'message' => 'Something wrong', 'data' => []);
		error_reporting(0);
		ini_set('display_errors', 0);
	}

	public function index_post()
	{
		$post = [
			'user_id' => $this->post('user_id'),
			'title' => $this->post('title'),
			'category_id' => $this->post('category_id'),
			'description' => $this->post('description')
		];

		if($post['user_id'] && $post['title'] && $post['category_id'] && $post['description']){

			$uploadProduct = $this->api_product_model->add_product($post);

			if ($uploadProduct) {
				if ($_FILES) {
					$productId = $uploadProduct;
					$this->uploadImg($productId);
				}

				$this->return['status'] = true;
				$this->return['message'] = "Success";
				$this->return['data'] = $uploadProduct;
			}else{
				return $this->response($this->return);
			}

		}else{
			$this->return['message'] = "Data tidak lengkap";
		}
		
		$this->response($this->return);
	}

	public function detailproduct_post()
	{
		$post = [
			'user_id' => $this->post('user_id'),
			'product_id' => $this->post('product_id'),
			'product_condition' => $this->post('product_condition'),
			'quantity' => $this->post('quantity'),
			'satuan' => $this->post('satuan'),
			'price' => $this->post('price'),
			'shipping_cost_type' => $this->post('shipping_cost_type'),
			'shipping_time' => $this->post('shipping_time'),
			'country_id' => $this->post('country_id'),
			'state_id' => $this->post('state_id'),
		];

		foreach ($post as $dataPost) {
			if(!$dataPost){
				$this->return['message'] = "Data tidak lengkap";
				return $this->response($this->return);
			}
		}

		$post['city_id'] = $this->post('city_id');
		$post['address'] = $this->post('address');
		$post['zip_code'] = $this->post('zip_code');

		$product = $this->product_admin_model->get_product($post['product_id']);

		if (!$product || ($post['user_id'] != $product->user_id)) {
			return $this->response($this->return);
		}

		if ($this->api_product_model->edit_product_details($post)) {
            //edit custom fields
            $this->api_product_model->update_product_custom_fields($post);

            $this->return['status'] = true;
            $this->return['message'] = "Success";
            unset($this->return['data']);
        }

		return $this->response($this->return);
	}

	public function drafedit_get()
	{
		$get = [
			'user_id' => $this->get('user_id'),
			'product_id' => $this->get('product_id')
		];

		$product = $this->product_admin_model->get_product($get['product_id']);

		if (!$product || ($get['user_id'] != $product->user_id)) {
			return $this->response($this->return);
		}

		$data['product'] = $product;

		$productImages = $this->api_file_model->get_product_images($product->id);
		$img = [];
		foreach ($productImages as $productImg) {
			$img[] = [
				'id' => $productImg->id,
				'product_id' => $productImg->product_id,
				'image_default' => getProductImageUrl($productImg, 'image_default'),
				'image_big' => getProductImageUrl($productImg, 'image_big'),
				'image_small' => getProductImageUrl($productImg, 'image_small'),
				'is_main' => $productImg->is_main
			];
		}
		$data["product_images"] = $img;
		$data["terms_conditions"] = 1;

		$this->return['status'] = true;
		$this->return['message'] = "Success";
		$this->return['data'] = $data;

		return $this->response($this->return);
	}

	public function drafedit_post()
	{
		$post = [
			'user_id' => $this->post('user_id'),
			'product_id' => $this->post('product_id'),
			'title' => $this->post('title'),
			'category_id' => $this->post('category_id'),
			'description' => $this->post('description')
		];

		foreach ($post as $dataPost) {
			if (!$dataPost) {
				$this->return['message'] = "Data tidak lengkap";
				return $this->response($this->return);
			}
		}

		$post['status_sold'] = $this->post('status_sold');

		$this->postEditStep1($post);
	}


	public function editproduct_get()
	{
		$get = [
			'user_id' => $this->get('user_id'),
			'product_id' => $this->get('product_id')
		];

		$product = $this->product_admin_model->get_product($get['product_id']);

		if (!$product || ($get['user_id'] != $product->user_id)) {
			return $this->response($this->return);
		}

		$product->is_sold_value = $product->is_sold == 0 ? 'active' : 'sold';

		$data['product'] = $product;

		$productImages = $this->api_file_model->get_product_images($product->id);
		$img = [];
		foreach ($productImages as $productImg) {
			$img[] = [
				'id' => $productImg->id,
				'product_id' => $productImg->product_id,
				'image_default' => getProductImageUrl($productImg, 'image_default'),
				'image_big' => getProductImageUrl($productImg, 'image_big'),
				'image_small' => getProductImageUrl($productImg, 'image_small'),
				'is_main' => $productImg->is_main
			];
		}
		$data["product_images"] = $img;

		$this->return['status'] = true;
		$this->return['message'] = "Success";
		$this->return['data'] = $data;

		return $this->response($this->return);
	}

	public function editproductstep2_get()
	{
		$get = [
			'user_id' => $this->get('user_id'),
			'product_id' => $this->get('product_id')
		];

		$product = $this->product_admin_model->get_product($get['product_id']);

		if (!$product || ($get['user_id'] != $product->user_id)) {
			return $this->response($this->return);
		}

		$data['product'] = $product;

		$productImages = $this->api_file_model->get_product_images($product->id);
		$img = [];
		foreach ($productImages as $productImg) {
			$img[] = [
				'id' => $productImg->id,
				'product_id' => $productImg->product_id,
				'image_default' => getProductImageUrl($productImg, 'image_default'),
				'image_big' => getProductImageUrl($productImg, 'image_big'),
				'image_small' => getProductImageUrl($productImg, 'image_small'),
				'is_main' => $productImg->is_main
			];
		}

		$sitelang = api_lang_helper()->id;

		$customFields = $this->api_field_model->generate_custom_fields_array($product->category_id, $product->subcategory_id, $product->third_category_id, $product->id, $sitelang);
		$data["product"]->unit = getCustomFieldValue2($customFields[0], $sitelang);
		$data["set_to_draft"] = $product->is_draft == 1 ? true : false;

		$this->return['status'] = true;
		$this->return['message'] = "Success";
		$this->return['data'] = $data;

		return $this->response($this->return);
	}

	public function postEditStep1($post)
	{
		$product = $this->product_admin_model->get_product($post['product_id']);

		if (!$product || ($post['user_id'] != $product->user_id)) {
			return $this->response($this->return);
		}

		if ($this->api_product_model->edit_product($post)) {
            //edit slug
            $this->product_model->update_slug($post['product_id']);

            if ($_FILES) {
				$productId = $post['product_id'];
				$this->uploadImg($productId);
			}

			$this->return['status'] = true;
			$this->return['message'] = "Success";
			$this->return['data'] = $productId;
        }

		$this->response($this->return);
	}

	public function editproduct_post()
	{
		$this->drafedit_post();
	}

	public function deleteimg_post()
	{
		$image_id = $this->input->post('image_id', true);

		$image = $this->file_model->get_image($image_id);
		if ($image) {
			$this->file_model->delete_product_image($image_id);

			$this->return['status'] = true;
			$this->return['message'] = "Success";
			unset($this->return['data']);
		}

		$this->response($this->return);
	}

	//set main image product
	public function setimage_post()
	{
		$image_id = $this->post('image_id');
		$product_id = $this->post('product_id');
		$image = $this->file_model->get_image($image_id);

		if ($image) {
			$this->file_model->set_image_main($image_id, $product_id);
			$this->return['status'] = true;
			$this->return['message'] = "Success";
			unset($this->return['data']);
		}

		$this->response($this->return);
	}

	public function uploadImg($productId)
	{
		$count = count($_FILES['files']['name']);
		for($i=0;$i<$count;$i++){
			if(!empty($_FILES['files']['name'][$i])){
				$_FILES['file']['name'] = $_FILES['files']['name'][$i];
				$_FILES['file']['type'] = $_FILES['files']['type'][$i];
				$_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
				$_FILES['file']['error'] = $_FILES['files']['error'][$i];
				$_FILES['file']['size'] = $_FILES['files']['size'][$i];

				$config['upload_path'] = 'uploads/temp/'; 
				$config['allowed_types'] = 'jpg|jpeg|png|gif';
				$config['max_size'] = '5000';
				$config['file_name'] = 'temp_product'. generate_unique_id();;

				$this->load->library('upload',$config);
				if($this->upload->do_upload('file')){
					$uploadData = $this->upload->data();
					
					$temp_path = $uploadData['full_path'];
					
					$this->api_upload_model->resizeImage($productId,$temp_path);

					$this->api_upload_model->delete_temp_image($temp_path);

				}

			}
		}
	}

}