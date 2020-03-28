<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Profile extends REST_Controller{

	public function __construct(){
        parent::__construct();
		
        $this->return = array('status' => false, 'message' => 'Something wrong', 'data' => []);
        $this->load->model("api_file_model");
        $this->load->model("api_profile_model");
        $this->load->model("api_review_model");
        $this->load->helper('api_helper');
        $this->load->helper('custom_helper');
        $this->product_per_page = 5;
        error_reporting(0);
        ini_set('display_errors', 0);
	}

	public function index_get()
	{
		$userSlug = $this->get('profile_slug');
		$userLogin = $this->get('user_login_slug');
		$slug = decode_slug($userSlug);
		$user = $this->auth_model->get_user_by_slug($slug);
		$userLogin_ = $this->auth_model->get_user_by_slug($userLogin);
		$perPage = 15;
		$offset = 0;

		if ($user) {
			$data['user'] = $user;
			$data['user']->avatar = getAvatar($user);
			$data['user']->aktif = timeAgo($user->last_seen);
			$data['user']->member_since = helper_date_format($user->created_at);
			$data['user']->shop_name = $user->shop_name ?: $user->username;
			$data['user']->seller_rating = 'Member';
			$data['user']->address = get_location($user);

			$data['count_product'] = get_user_products_count($user->slug);
			$data['count_favorite'] = get_user_favorited_products_count($user->id);
			$data['count_followers'] = get_followers_count($user->id);
			$data['count_following'] = get_following_users_count($user->id);
			$data['count_review'] = $this->api_review_model->get_review_user_count($user->id);

			if ($userLogin == $userSlug) {
				$data['count_pending'] = get_user_pending_products_count($user->id);
				$data['count_hidden'] = get_user_hidden_products_count($user->id);
				$data['count_drafts'] = get_user_drafts_count($user->id);
			}

			if ($userLogin != $userSlug) {
				$data['is_follow'] = is_user_follows($user->id, $userLogin_->id);
			}

			$this->return['status'] = true;
			$this->return['message'] = "Success";
			$this->return['data'] = $data;
		}else{
			$this->return['message'] = "User not found";
		}

        $this->response($this->return);
	}

	public function listproduct_get()
	{
		$userSlug = $this->get('user_slug');
    	$slug = decode_slug($userSlug);
        $user = $this->auth_model->get_user_by_slug($slug);

        $page = $this->get('page') ?: '1';
        $perPage = $this->get('per_page') ?: $this->product_per_page;
        $offset = $perPage * ($page - 1);

        if (empty($user)) {
            $this->return['message'] = "User not found";
        }else {
        	$products = $this->product_model->get_paginated_user_products($user->slug, $perPage, $offset);
        	$datas = [];
			foreach ($products as $productValue) {
				$dataProduct = listdataProduct($productValue);

				$image = $this->api_file_model->get_image_by_product($productValue->id);
				$dataProduct['image'] = generateImgProduct($image,'image_small');

				$datas[] = $dataProduct;
			}
            $data['count_drafts'] = get_user_products_count($user->slug);
            $data['total_page'] = ceil(get_user_products_count($user->slug) / $perPage);
			$data['product'] = $datas;

			$this->return['status'] = true;
			$this->return['message'] = "Success";
			$this->return['data'] = $data;
        }

        $this->response($this->return);
	}

	public function listfavorite_get()
    {
    	$userSlug = $this->get('user_slug');
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

    public function follower_get()
    {
    	$userSlug = $this->get('user_slug');
    	$slug = decode_slug($userSlug);
        $user = $this->auth_model->get_user_by_slug($slug);

        if (empty($user)) {
            $this->return['message'] = "User not found";
        }else {
        	$follower = $this->profile_model->get_followers($user->id);

        	if ($follower) {
        		$followers = [];
        		foreach ($follower as $user) {
        			$followerList = userDataList($user);
        			$followerList['avatar'] = getAvatar($user);

        			$followers[] = $followerList;
        		}

        		$this->return['status'] = true;
        		$this->return['message'] = "Success";
        		$this->return['data'] = $followers;
        	}else{
        		$this->return['status'] = true;
        		$this->return['message'] = "Success";
        		$this->return['data'] = [];
        	}
        }

        $this->response($this->return);
    }

    public function following_get()
    {
    	$userSlug = $this->get('user_slug');
    	$slug = decode_slug($userSlug);
        $user = $this->auth_model->get_user_by_slug($slug);

        if (empty($user)) {
            $this->return['message'] = "User not found";
        }else {
        	$following = $this->profile_model->get_following_users($user->id);

        	if ($following) {
        		$followings = [];
        		foreach ($following as $user) {
        			$followingList = userDataList($user);
        			$followingList['avatar'] = getAvatar($user);

        			$followings[] = $followingList;
        		}

        		$this->return['status'] = true;
        		$this->return['message'] = "Success";
        		$this->return['data'] = $followings;
        	}else{
        		$this->return['status'] = true;
        		$this->return['message'] = "Success";
        		$this->return['data'] = [];
        	}
        }

        $this->response($this->return);
    }

    public function followunfollow_post()
    {
    	$data = [
    		'follower_id' => $this->post('user_id'),
    		'following_id' => $this->post('following_id')
    	];

    	if ($this->post('user_id') && $this->post('following_id')) {

    		$this->api_profile_model->follow_unfollow_user($data);

    		$this->return['status'] = true;
    		$this->return['message'] = "Success";
    	} else {
    		$this->return['message'] = "Invalid data";
    	}

    	$this->response($this->return);
    }

    public function draft_get($value='')
    {
        $page = $this->get('page') ?: '1';
        $perPage = $this->get('per_page') ?: $this->product_per_page;
        $offset = $perPage * ($page - 1);

        $userSlug = $this->get('user_slug');
        $slug = decode_slug($userSlug);
        $user = $this->auth_model->get_user_by_slug($slug);

        $data['count_drafts'] = get_user_drafts_count($user->id);
        $data['total_page'] = ceil(get_user_drafts_count($user->id) / $perPage);
        $products = $this->product_model->get_paginated_user_drafts($user->id, $perPage, $offset);

        $datas = [];
        foreach ($products as $productValue) {
            $dataProduct = listdataProduct($productValue);

            $image = $this->api_file_model->get_image_by_product($productValue->id);
            $dataProduct['image'] = generateImgProduct($image,'image_small');


            $datas[] = $dataProduct;
        }
        $data['product'] = $datas;

        $this->return['status'] = true;
        $this->return['message'] = "Success";
        $this->return['data'] = $data;

        $this->response($this->return);
    }

    public function review_get()
    {
        $userSlug = $this->get('user_slug');
        $slug = decode_slug($userSlug);
        $user = $this->auth_model->get_user_by_slug($slug);

        $listProduct = $this->api_review_model->get_reviews_user($user->id);
        $reviewProducts = [];
        if ($listProduct) {
            foreach ($listProduct as $products) {
                $product = $this->product_model->get_products_by_product_id($products->product_id);
                // print_r($product);
                $reviewProduct = listdataProduct($product);
                $image = $this->api_file_model->get_image_by_product($product->id);
                $reviewProduct['image'] = generateImgProduct($image, 'image_small');
                $reviewProducts[] = $reviewProduct;
            }
        }
        $data['products'] = $reviewProducts;

        $this->return['status'] = true;
        $this->return['message'] = "Success";
        $this->return['data'] = $data;

        $this->response($this->return);
    }

}