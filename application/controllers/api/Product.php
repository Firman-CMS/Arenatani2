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
		$this->load->helper('api_helper');
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
			$dataProduct = $this->dataProduct($productValue);
			$dataProduct['image'] = $this->image_get($productValue->id, 'image_small');

			$datas[] = $dataProduct;
		}
		$data['product'] = $datas;
		
		$sitelang = api_lang_helper()->id; //call from api_helper
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

	public function get_product_image_url($image, $size)
    {
        if ($image) {
        	return base_url() . "uploads/images/" . $image->$size;
        } else {
        	return base_url() . 'assets/img/no-image.jpg';
        }
    }

	public function detail_get()
	{
		$this->review_limit = 5;
		$this->comment_limit = 5;

		$slug = $this->get('slug');
		$userId = $this->get('user_id');
		$productValue = $this->product_model->get_product_by_slug($slug);

		$datas = [];
		if ($productValue->id) {
			$price = $productValue->price / 100;
		
			$datas = $this->dataProduct($productValue);
			$data["product"] = $datas;

			$productImages = $this->api_file_model->get_product_images($productValue->id);
			$img = [];
			foreach ($productImages as $productImg) {
				$img[] = [
					'id' => $productImg->id,
					'product_id' => $productImg->product_id,
					'image_default' => $this->get_product_image_url($productImg, 'image_default'),
					'image_big' => $this->get_product_image_url($productImg, 'image_big'),
					'image_small' => $this->get_product_image_url($productImg, 'image_small')
				];
			}
			$data["product_images"] = $img;

			$sitelang = api_lang_helper()->id; //call from api_helper
			$data["category"] = (array) $this->api_category_model->get_category_joined($productValue->category_id, $sitelang);
			$data["subcategory"] = (array) $this->api_category_model->get_category_joined($productValue->subcategory_id, $sitelang);
            $data["third_category"] = (array) $this->api_category_model->get_category_joined($productValue->third_category_id, $sitelang);
            
            $relatedProducts = $this->api_product_model->get_related_products($productValue);
            $relatedProduct_ = [];
            foreach ($relatedProducts as $relatedProduct) {
            	$relatedList = $this->dataProduct($relatedProduct);
            	$relatedList['image'] = $this->image_get($relatedProduct->id, 'image_small');
            	$relatedProduct_[] = $relatedList;
            }
            $data["related_products"] = $relatedProduct_;

            $data["user"] = (array) $this->auth_model->get_user($productValue->user_id);
            $data["user"]["avatar"] = $data["user"]["avatar"] ? base_url().$data["user"]["avatar"] : '';
            
            $userProducts = $this->product_model->get_user_products($data["user"]['slug'], 3, $data["product"]['id']);
            $userProductList = [];
            foreach ($userProducts as $userProduct) {
            	$userProdList = $this->dataProduct($userProduct);
            	$userProdList['image'] = $this->image_get($userProduct->id, 'image_small');
            	$userProductList[] = $userProdList;
            }
            $data["user_products"] = $userProductList;

            $data['review_count'] = $this->review_model->get_review_count($productValue->id);
            $data['reviews'] = $this->review_model->get_limited_reviews($productValue->id, $this->review_limit);
            $data['review_limit'] = $this->review_limit;
            $sumRating = 0;
            if ($data['review_count']) {
            	foreach ($data['reviews'] as $reviewCount) {
            		$sumRating+= $reviewCount->rating;
            	}
            	$data['review_rating'] = $sumRating / $data['review_count'];
            }

            $data['comment_count'] = $this->comment_model->get_product_comment_count($productValue->id);
            $data['comments'] = $this->comment_model->get_comments($productValue->id, $this->comment_limit);
            $data['comment_limit'] = $this->comment_limit;

            $data['is_favorite'] = $this->isfavorite($userId, $productValue->id);
            $data['favorite_count'] = $this->product_model->get_product_favorited_count($productValue->id);
            $data['hit_count'] = $productValue->hit;

            $data['location_maps'] = $this->buildLocation($productValue);

			$this->return['status'] = true;
			$this->return['message'] = "Success";
			$this->return['data'] = $data;
		} else {
			$this->return['message'] = "No data";
		}
		
		$this->response($this->return);
	}

	public function dataProduct($objProduct)
	{
		$datas = [
			'id' => $objProduct->id,
			'title' => $objProduct->title,
			'slug' => $objProduct->slug,
			'image' => $objProduct->image,
			'penjual' => $objProduct->penjual,
			'provinsi' => $objProduct->provinsi,
			'kabupaten' => $objProduct->kabupaten,
			'kecamatan' => $objProduct->kecamatan,
			'hape' => $objProduct->hape,
			'photo_profile' => $objProduct->photo_profile,
			'product_type' => $objProduct->product_type,
			'listing_type' => $objProduct->listing_type,
			'category_id' => $objProduct->category_id,
			'subcategory_id' => $objProduct->subcategory_id,
			'third_category_id' => $objProduct->third_category_id,
			'price' => $objProduct->price / 100,
			'currency' => $objProduct->currency,
			'description' => $objProduct->description,
			'product_condition' => $objProduct->product_condition,
			'country_id' => $objProduct->country_id,
			'state_id' => $objProduct->state_id,
			'city_id' => $objProduct->city_id,
			'address' => $objProduct->address,
			'zip_code' => $objProduct->zip_code,
			'user_id' => $objProduct->user_id,
			'status' => $objProduct->status,
			'is_promoted' => $objProduct->is_promoted,
			'promote_start_date' => $objProduct->promote_start_date,
			'promote_end_date' => $objProduct->promote_end_date,
			'promote_plan' => $objProduct->promote_plan,
			'promote_day' => $objProduct->promote_day,
			'visibility' => $objProduct->visibility,
			'rating' => $objProduct->rating,
			'hit' => $objProduct->hit,
			'external_link' => $objProduct->external_link,
			'files_included' => $objProduct->files_included,
			'shipping_time' => $objProduct->shipping_time,
			'shipping_cost_type' => $objProduct->shipping_cost_type,
			'shipping_cost' => $objProduct->shipping_cost,
			'is_sold' => $objProduct->is_sold,
			'is_deleted' => $objProduct->is_deleted,
			'is_draft' => $objProduct->is_draft,
			'created_at' => $objProduct->created_at,
			'user_username' => $objProduct->user_username,
			'shop_name' => $objProduct->shop_name,
			'user_role' => $objProduct->user_role,
			'user_slug' => $objProduct->user_slug,
			'product_url' => base_url().'/'.$objProduct->slug,
		];

		return $datas;
	}

	public function get_location($object)
    {
        $location = "";
        if (!empty($object)) {
            if (!empty($object->address)) {
                $location = $object->address;
            }
            if (!empty($object->zip_code)) {
                $location .= " " . $object->zip_code;
            }
            if (!empty($object->city_id)) {
                $city = $this->location_model->get_city($object->city_id);
                if (!empty($city)) {
                    if (!empty($object->address) || !empty($object->zip_code)) {
                        $location .= " ";
                    }
                    $location .= $city->name;
                }
            }
            if (!empty($object->state_id)) {
                $state = $this->location_model->get_state($object->state_id);
                if (!empty($state)) {
                    if (!empty($object->address) || !empty($object->zip_code) || !empty($object->city_id)) {
                        $location .= ", ";
                    }
                    $location .= $state->name;
                }
            }
            if (!empty($object->country_id)) {
                $country = $this->location_model->get_country($object->country_id);
                if (!empty($country)) {
                    if (!empty($object->state_id) || $object->city_id || !empty($object->address) || !empty($object->zip_code)) {
                        $location .= ", ";
                    }
                    $location .= $country->name;
                }
            }
        }
        return $location;
    }

    public function buildLocation($object)
    {
    	$location = $this->get_location($object);
    	$frame = '';
    	if ($location) {
    		$frame = '<iframe src="https://maps.google.com/maps?width=100%&height=600&hl=en&q='.$location .'&ie=UTF8&t=&z=8&iwloc=B&output=embed&disableDefaultUI=true" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>';
    	}
    	return $frame;
    }

    public function favorite_post()
    {
    	$data = [
			'user_id' => $this->post('user_id'),
			'product_id' => $this->post('product_id')
		];

		if ($this->post('user_id')) {

			$this->api_product_model->add_remove_favorites($data);

			$this->return['status'] = true;
			$this->return['message'] = "Success";
		} else {
			$this->return['message'] = "Invalid data";
		}

		$this->response($this->return);
    }

    public function isfavorite($userId, $productId)
    {
    	if (!$userId) {
    		return false;
    	}

    	$data = [
			'user_id' => $userId,
			'product_id' => $productId
		];

    	$isFavorite = $this->api_product_model->is_product_in_favorites($data);

    	if ($isFavorite) {
    		return true;
    	}else{
    		return false;
    	}
    }
}
?>
