<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Product extends REST_Controller{

	public function __construct(){
		parent::__construct();
		$this->return = array('status' => false, 'message' => 'Something wrong', 'data' => []);

		$this->load->model("api_product_model");
		$this->load->model("api_category_model");
		$this->load->model("api_file_model");
		$this->load->model("api_general_settings");
		$this->product_per_page = 15;
	}

	public function index_get(){
		$page = $this->get('page') ?: '1';
		$perPage = $this->get('per_page') ?: $this->product_per_page;
		$offset = $perPage * ($page - 1);

		$getData = [
			'condition' => $this->get('condition'),
			'p_min' => $this->get('p_min'),
			'p_max' => $this->get('p_max'),
			'sort' => $this->get('sort'),
			'search' => $this->get('search'),
			'country' => $this->get('country'),
			'state' => $this->get('state'),
			'city' => $this->get('city')
		];
		
		$data['total'] = $this->api_product_model->get_paginated_filtered_products_count(null, null, null, $getData);
		$products = $this->api_product_model->get_paginated_filtered_products(null, null, null, $perPage, $offset, $getData);

		$datas = [];
		foreach ($products as $productValue) {
			$price = $productValue->price / 100;
			$imageProduct = $this->image_get($productValue->id, 'image_small');
			$datas[] = [
				'id' => $productValue->id,
				'title' => $productValue->title,
				'slug' => $productValue->slug,
				'image' => $imageProduct,
				'penjual' => $productValue->penjual,
				'provinsi' => $productValue->provinsi,
				'kabupaten' => $productValue->kabupaten,
				'kecamatan' => $productValue->kecamatan,
				'hape' => $productValue->hape,
				'photo_profile' => $productValue->photo_profile,
				'product_type' => $productValue->product_type,
				'listing_type' => $productValue->listing_type,
				'category_id' => $productValue->category_id,
				'subcategory_id' => $productValue->subcategory_id,
				'third_category_id' => $productValue->third_category_id,
				'price' => $price,
				'currency' => $productValue->currency,
				'description' => $productValue->description,
				'product_condition' => $productValue->product_condition,
				'country_id' => $productValue->country_id,
				'state_id' => $productValue->state_id,
				'city_id' => $productValue->city_id,
				'address' => $productValue->address,
				'zip_code' => $productValue->zip_code,
				'user_id' => $productValue->user_id,
				'status' => $productValue->status,
				'is_promoted' => $productValue->is_promoted,
				'promote_start_date' => $productValue->promote_start_date,
				'promote_end_date' => $productValue->promote_end_date,
				'promote_plan' => $productValue->promote_plan,
				'promote_day' => $productValue->promote_day,
				'visibility' => $productValue->visibility,
				'rating' => $productValue->rating,
				'hit' => $productValue->hit,
				'external_link' => $productValue->external_link,
				'files_included' => $productValue->files_included,
				'shipping_time' => $productValue->shipping_time,
				'shipping_cost_type' => $productValue->shipping_cost_type,
				'shipping_cost' => $productValue->shipping_cost,
				'is_sold' => $productValue->is_sold,
				'is_deleted' => $productValue->is_deleted,
				'is_draft' => $productValue->is_draft,
				'created_at' => $productValue->created_at,
				'user_username' => $productValue->user_username,
				'shop_name' => $productValue->shop_name,
				'user_role' => $productValue->user_role,
				'user_slug' => $productValue->user_slug,
				'product_url' => base_url().'/'.$productValue->slug,
			];
		}
		$data['product'] = $datas;
		
		$sitelang = $this->api_general_settings->getValueOf('site_lang');
		$categoryList = $this->api_category_model->get_parent_categories($sitelang);
		$cat = [];
		foreach ($categoryList as $category) {
			$cat[] = [
				'id' => $category->id,
				'slug' => $category->slug,
				'name' => $category->name,
				'lang_id' => $category->lang_id,
				'count_product' => $this->api_product_model->get_paginated_filtered_products_count($category->id, null, null, $getData)
			];
		}
		
		$data['categories'] = $cat;
		$data['total_per_page'] = count($datas);

		if ($data['product']) {
			$this->return['status'] = true;
			$this->return['message'] = "Success";
			$this->return['data'] = $data;
		}else {
			$this->return['message'] = "No data";
		}
		// print_r($data["categories"]);
		$this->response($this->return);
	}

	public function image_get($productId, $size)
	{
		$image = $this->api_file_model->get_image_by_product($productId);
		if (empty($image)) {
            return base_url() . 'assets/img/no-image.jpg';
        } else {
            return base_url() . "uploads/images/" . $image->$size;
        }
	}
}
?>
