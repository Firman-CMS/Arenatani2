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
        $this->load->model("api_field_model");
        $this->load->model("api_review_model");
        $this->load->helper('api_helper');
        $this->load->helper('custom_helper');
        $this->product_per_page = 15;
        error_reporting(0);
        ini_set('display_errors', 0);
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
    		$dataProduct = listdataProduct($productValue);

    		$image = $this->api_file_model->get_image_by_product($productValue->id);
    		$dataProduct['image'] = generateImgProduct($image,'image_small');


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
    	
    		$datas = listdataProduct($productValue);
    		$data["product"] = $datas;
    		$data["product"]["uploaded"] = timeAgo($productValue->created_at);

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
            	$relatedList = listdataProduct($relatedProduct);

            	$image = $this->api_file_model->get_image_by_product($relatedProduct->id);
            	$relatedList['image'] = generateImgProduct($image,'image_small');
            	
            	$relatedProduct_[] = $relatedList;
            }
            $data["related_products"] = $relatedProduct_;

            $data["user"] = $this->auth_model->get_user($productValue->user_id);
            $data["user"]->avatar = getAvatar($data["user"]);
            $data["user"]->aktif = timeAgo($data["user"]->last_seen);
            if ($userId != $data["user"]->id) {
    			$data["user"]->is_follow = is_user_follows($data["user"]->id, $userId);
    		}
            
            $userProducts = $this->product_model->get_user_products($data["user"]->slug, 3, $data["product"]['id']);
            $userProductList = [];
            foreach ($userProducts as $userProduct) {
            	$userProdList = listdataProduct($userProduct);
            	
            	$image = $this->api_file_model->get_image_by_product($userProduct->id);
            	$userProdList['image'] = generateImgProduct($image,'image_small');
            	
            	$userProductList[] = $userProdList;
            }
            $data["user_products"] = $userProductList;

            $data['review_count'] = $this->review_model->get_review_count($productValue->id);
            
            $reviews = $this->review_model->get_limited_reviews($productValue->id, $this->review_limit);
            $dataReview = [];
            foreach ($reviews as $listReview) {
                $listReview->date =timeAgo($listReview->created_at);
                $user = $this->auth_model->get_user($listReview->user_id);
                $listReview->shop_name = $user->shop_name ?: $user->username;
                $listReview->avatar = getAvatar($user);
                $listReview->can_delete = false;
                if($userId && $userId == $listReview->user_id){
                    $listReview->can_delete = true;
                }

                $dataReview[] = $listReview;
            }
            $data['reviews'] = $dataReview;
            $data['review_limit'] = $this->review_limit;
            $sumRating = 0;
            if ($data['review_count']) {
            	foreach ($data['reviews'] as $reviewCount) {
            		$sumRating+= $reviewCount->rating;
            	}
            	$data['review_rating'] = $sumRating / $data['review_count'];
            }

            $data['comment_count'] = $this->comment_model->get_product_comment_count($productValue->id);
            $comments = $this->comment_model->get_comments($productValue->id, $this->comment_limit);
            $dataComment = [];
            foreach ($comments as $listComments) {
                $listComments->date =timeAgo($listComments->created_at);
                $user = $this->auth_model->get_user($listComments->user_id);
                $listComments->shop_name = $user->shop_name ?: $user->username;
                $listComments->avatar = getAvatar($user);
                $listComments->can_delete = false;
                if($userId && $userId == $listComments->user_id){
                    $listComments->can_delete = true;
                }

                $subcomments = get_subcomments($listComments->id);
                $dataSubcomments = [];
                foreach ($subcomments as $listSubcomments) {
                    $listSubcomments->date =timeAgo($listSubcomments->created_at);
                    $user = $this->auth_model->get_user($listSubcomments->user_id);
                    $listSubcomments->shop_name = $user->shop_name ?: $user->username;
                    $listSubcomments->avatar = getAvatar($user);
                    $listSubcomments->can_delete = false;
                    if($userId && $userId == $listSubcomments->user_id){
                        $listSubcomments->can_delete = true;
                    }

                    $dataSubcomments[] = $listSubcomments;
                }
                $listComments->sub_comment = $dataSubcomments;
                
                $dataComment[] = $listComments;
            }
            $data['comments'] = $dataComment;
            $data['comment_limit'] = $this->comment_limit;

            $data['is_favorite'] = $this->isfavorite($userId, $productValue->id);
            $data['favorite_count'] = $this->product_model->get_product_favorited_count($productValue->id);
            $data['hit_count'] = $productValue->hit;

            $data['location_maps'] = $this->buildLocation($productValue);

            $customFields = $this->api_field_model->generate_custom_fields_array($productValue->category_id, $productValue->subcategory_id, $productValue->third_category_id, $productValue->id, $sitelang);
            
            $data["product"]["unit"] = getCustomFieldValue($customFields[0], $sitelang);
            $data["product"]["address_detail"] = getLocation($productValue);
            $data["product"]["product_condition"] = get_product_condition_by_key($productValue->product_condition, $sitelang);

    		$this->return['status'] = true;
    		$this->return['message'] = "Success";
    		$this->return['data'] = $data;
    	} else {
    		$this->return['message'] = "No data";
    	}
    	
    	$this->response($this->return);
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

    public function setproductsold_post()
    {
        $product_id = $this->post('product_id');
        $userId = $this->post('user_id');

        $product = $this->product_admin_model->get_product($product_id);

        if (!$product) {
        	return $this->response($this->return);
        }

        if ($product->user_id != $userId || $product->is_draft == 1) {
        	return $this->response($this->return);
        }

        $this->api_product_model->set_product_as_sold($product_id);

        $this->return['status'] = true;
        $this->return['message'] = "Success";
        unset($this->return['data']);

        $this->response($this->return);
    }

    public function delproduct_post()
    {
    	$product_id = $this->post('product_id');
    	$userId = $this->post('user_id');

    	$product = $this->api_product_model->get_product_by_id($product_id);

    	if (!$product) {
    		return $this->response($this->return);
    	}

    	if ($product->user_id != $userId) {
    		return $this->response($this->return);
    	}

    	if ($product->is_deleted == 1) {
    		$this->return['message'] = "Telah dihapus";
    		return $this->response($this->return);
    	}

    	$this->api_product_model->delete_product($product_id);

    	$this->return['status'] = true;
    	$this->return['message'] = "Success";
    	unset($this->return['data']);

    	$this->response($this->return);
    }

    public function addreview_post()
    {
        $data = [
            'user_id' => $this->post('user_id'),
            'product_id' => $this->post('product_id'),
            'review' => $this->post('review'),
            'rating' => $this->post('rating')
        ];

        if (!$data['product_id'] || !$data['user_id'] || !$data['rating']) {
            $this->return['message'] = "Data tidak lengkap";
            unset($this->return['data']);
            return $this->response($this->return);
        }

        if (!$data['user_id'] || $this->api_general_settings->getValueOf('product_reviews') != 1) {
            return $this->response($this->return);
        }

        $review = $this->review_model->get_review($data['product_id'], $data['user_id']);
        if ($review) {
            $this->return['message'] = "Anda sudah menulis ulasan sebelumnya!";
            unset($this->return['data']);
            return $this->response($this->return);
        }

        $product = $this->product_model->get_product_by_id($data['product_id']);
        if ($product->user_id == $data['user_id']) {
            $this->return['message'] = "Anda tidak dapat menilai produk Anda sendiri!";
            unset($this->return['data']);
            return $this->response($this->return);
        }

        $this->api_review_model->add_review($data);

        $this->return['status'] = true;
        $this->return['message'] = "Success";
        unset($this->return['data']);

        $this->response($this->return);

    }

    public function delreview_post()
    {
        $data = [
            'user_id' => $this->post('user_id'),
            'product_id' => $this->post('product_id'),
            'id' => $this->post('review_id')
        ];

        $review = $this->review_model->get_review($data['product_id'], $data['user_id']);

        if ($review && ($data['user_id'] == $review->user_id)) {
                $this->review_model->delete_review($data['id'], $data['product_id']);

                $this->return['status'] = true;
                $this->return['message'] = "Success";
                unset($this->return['data']);
        }

        $this->response($this->return);

    }

    public function addcomment_post()
    {
        $post = [
            'user_id' => $this->post('user_id'),
            'product_id' => $this->post('product_id'),
            'parent_id' => $this->post('parent_id'),
            'comment' => $this->post('comment')
        ];

        if ($this->api_general_settings->getValueOf('product_comments') != 1) {
            $this->return['message'] = "Tidak dapat membuat komentar";
            unset($this->return['data']);
            $this->response($this->return);
        }

        if (!$post['user_id']) {
            $post['user_id'] = '0';
            $post['name'] = $this->post('name');
            $post['email'] = $this->post('email');
            if (!$post['name'] || !$post['email'] || !$post['comment']) {
                $this->return['message'] = "Data Tidak Lengkap 1";
                unset($this->return['data']);
                $this->response($this->return);
            }
        }else{
            if (!$post['product_id'] || !$post['comment']) {
                $this->return['message'] = "Data Tidak Lengkap";
                unset($this->return['data']);
                $this->response($this->return);
            }
            $post['name'] = '';
            $post['email'] = '';
        }

        if (!$post['parent_id']) {
            $post['parent_id'] = '0';
        }

        $this->comment_model->add_comment_api($post);

        $this->return['status'] = true;
        $this->return['message'] = "Success";
        unset($this->return['data']);

        $this->response($this->return);
    }

    public function dellcomment_post()
    {
        $id = $this->post('comment_id');
        $userId = $this->post('user_id');

        $comment = $this->comment_model->get_comment($id);
        $user = $this->auth_model->get_user($userId);
        if ($user && $comment) {
            if ($user->id == $comment->user_id) {
                $this->comment_model->delete_comment($id);

                $this->return['status'] = true;
                $this->return['message'] = "Success";
                unset($this->return['data']);
            }
        }

        $this->response($this->return);
    }

    public function getcondition_get()
    {
        $product_conditions = get_active_product_conditions(api_lang_helper()->id);
        $productCondition[] = ['value' => '', 'label' => 'Pilih satu opsi'];
        foreach ($product_conditions as $value) {
            $data = [
                'value' => $value->option_key,
                'label' => $value->option_label,
            ];

            array_push($productCondition, $data);
        }
        $this->return['status'] = true;
        $this->return['message'] = "Success";
        $this->return['data'] = $productCondition;

        $this->response($this->return);
    }

    public function getsatuan_get()
    {
        $satuan = $this->api_field_model->get_field_options('2',api_lang_helper()->id);
        $satuanOptions[] = ['value' => '', 'label' => 'Pilih satu opsi'];
        foreach ($satuan as $options) {
            $data = [
                'value' => $options->common_id,
                'label' => $options->field_option,
            ];

            array_push($satuanOptions, $data);
        }
        $this->return['status'] = true;
        $this->return['message'] = "Success";
        $this->return['data'] = $satuanOptions;

        $this->response($this->return);
    }

    public function getshippingtype_get()
    {
        $shippingType[0] = ['value' => '', 'label' => 'Pilih satu opsi'];
        $shippingType[1] = ['value' => 'COD', 'label' => 'COD'];

        $this->return['status'] = true;
        $this->return['message'] = "Success";
        $this->return['data'] = $shippingType;

        $this->response($this->return);
    }

    public function getshippingtime_get()
    {
        $shippingTime[0] = ['value' => '', 'label' => 'Pilih satu opsi'];
        $shippingTime[1] = ['value' => '1_business_day', 'label' => trans("1_business_day")];
        $shippingTime[2] = ['value' => '2_3_business_days', 'label' => trans("2_3_business_days")];
        $shippingTime[3] = ['value' => '4_7_business_days', 'label' => trans("4_7_business_days")];
        $shippingTime[4] = ['value' => '8_15_business_days', 'label' => trans("8_15_business_days")];

        $this->return['status'] = true;
        $this->return['message'] = "Success";
        $this->return['data'] = $shippingTime;

        $this->response($this->return);
    }

    public function getcountry_get()
    {
        $country[] = ['value' => '102', 'label' => 'Indonesia'];

        $this->return['status'] = true;
        $this->return['message'] = "Success";
        $this->return['data'] = $country;

        $this->response($this->return);
    }

    public function getstate_get()
    {
        $states[] = ['value' => '', 'label' => 'Provinsi'];
        $state = $this->location_model->get_states_by_country('102');

        foreach ($state as $options) {
            $data = [
                'value' => $options->id,
                'label' => $options->name,
            ];

            array_push($states, $data);
        }

        $this->return['status'] = true;
        $this->return['message'] = "Success";
        $this->return['data'] = $states;

        $this->response($this->return);
    }

    public function getcity_get()
    {
        $stateId = $this->get('state_id');

        $city = $this->location_model->get_cities_by_state($stateId);
        $cities[] = ['value' => '', 'label' => 'Kota'];
        foreach ($city as $options) {
            $data = [
                'value' => $options->id,
                'label' => $options->name,
            ];

            array_push($cities, $data);
        }

        $this->return['status'] = true;
        $this->return['message'] = "Success";
        $this->return['data'] = $cities;

        $this->response($this->return);
    }
}
?>
