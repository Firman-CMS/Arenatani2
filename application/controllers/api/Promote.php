<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Promote extends REST_Controller{

	public function __construct(){
		parent::__construct();
		$this->return = array('status' => false, 'message' => 'Something wrong');

		$this->load->model("api_file_model");
		$this->load->helper('api_helper');
		// $this->load->model("payment_settings");
		// $this->load->model("api_user_model");
	}

	public function plan_get(){
		$payment_settings = $this->settings_model->get_payment_settings();
		$data = [
			['type'=>'daily_plan', 'price'=> price_format_decimal($payment_settings->price_per_day)],
			['type'=>'weekly_plan', 'price'=> price_format_decimal($payment_settings->price_per_week)],
			['type'=>'monthly_plan', 'price'=> price_format_decimal($payment_settings->price_per_month)]
		];
		
		$this->return['status'] = true;
		$this->return['message'] = "Success";
		$this->return['data'] = $data;

		$this->response($this->return);
	}

	public function plan_post(){
        $productId = $this->get('product_id');
        $planType = $this->get('plan_type');
        $duration = $this->get('duration');

        $payment_settings = $this->settings_model->get_payment_settings();

		$price_per_day = price_format_decimal($payment_settings->price_per_day);
        $price_per_week = price_format_decimal($payment_settings->price_per_week);
        $price_per_month = price_format_decimal($payment_settings->price_per_month);
        
        if ($planType == 'daily_plan') {
        	$price = $price_per_day;
        	$purchased_plan = trans("daily_plan") . " (" . $duration . " " . trans("days") . ")";
        }elseif ($planType == 'weekly_plan') {
        	$day_count = $duration * 7;
        	$price = $price_per_week;
        	$purchased_plan = trans("weekly_plan") . " (" . $day_count . " " . trans("days") . ")";
        }elseif ($planType == 'monthly_plan'){
        	$day_count = $duration * 30;
        	$price = $price_per_month;
        	$purchased_plan = trans("monthly_plan") . " (" . $day_count . " " . trans("days") . ")";
        }
        $total = $price * $duration * 100;
        $product = $this->product_admin_model->get_product($productId);
        $user = $this->auth_model->get_user($product->user_id);
        
        $data = [
        	'product_id' => $productId,
        	'purchased_plan' => $purchased_plan,
        	'total_amount' => $total,
        	'user' => $user,
        	'plan' => $planType,
        	'duration' => $duration,
        ];

        $this->ipaymu($data);
	}

	public function ipaymu($data)
	{
		$url = 'https://my.ipaymu.com/payment.htm';
		$test = base_url() . 'api/promote/payment?product_id='.$data['product_id'].'&plan='.$data['purchased_plan'].'&total='.$data['total_amount'].'&duration='.$data['duration'];
		// Prepare Parameters
		$params = array(
			'key'      => '57231044-80B0-4739-8FF8-FFC3C71403AB', // API Key Merchant / Penjual
			// 'key'      => 'C68D98E0-02CB-472D-9C43-5540E0CA5A99', // API Key Merchant / Penjual
			'action'   => 'payment',
			'product'  => $data['purchased_plan'],
			'price'    => price_format_decimal($data['total_amount']), // Total Harga
			'quantity' => 1,
			'comments' => 'Pembelian paket promosi produk', // Optional           
			'ureturn'  => base_url() . 'api/promote/payment?product_id='.$data['product_id'].'&plan='.$data['plan'].'&total='.$data['total_amount'].'&duration='.$data['duration'],
			'unotify'  => base_url(),
			'ucancel'  => base_url(),

			/* Parameter untuk pembayaran lain menggunakan PayPal 
			* ----------------------------------------------- */
			'buyer_name'  => $data['user']->slug, // Nama customer/pembeli(opsional) 
			'buyer_phone' => $data['user']->phone_number, // No HP customer/pembeli (opsional)
			'buyer_email' => $data['user']->email, // Email customer/pembeli (opsional)

			/* ----------------------------------------------- */

			'format'   => 'json' // Format: xml / json. Default: xml 
		);

		$params_string = http_build_query($params);

		//open connection
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		//execute post
		$request = curl_exec($ch);

		if ( $request === false ) {
			echo 'Curl Error: ' . curl_error($ch);
		} else {

			$result = json_decode($request, true);
			if( isset($result['url']) )
				header('location: '. $result['url']);
			else {
				return $this->response($this->return);
			}
		}

		//close connection
		curl_close($ch);
	}

	public function payment_get()
	{
		$get = $this->get();
		
		if ($get['product_id']) {
			$product = $this->product_admin_model->get_product($get['product_id']);

			$image = $this->api_file_model->get_image_by_product($product->id);
			$data['product_name'] = $product->title;
			$data['image'] = generateImgProduct($image,'image_small');
			
			if ($get['plan'] == 'daily_plan') {
				$duration = $get['duration'];
	        	$purchased_plan = trans("daily_plan") . " (" . $duration . " " . trans("days") . ")";
	        }elseif ($get['plan'] == 'weekly_plan') {
	        	$duration = $get['duration'] * 7;
	        	$purchased_plan = trans("weekly_plan") . " (" . $duration . " " . trans("days") . ")";
	        }elseif ($get['plan'] == 'monthly_plan'){
	        	$duration = $get['duration'] * 30;
	        	$purchased_plan = trans("monthly_plan") . " (" . $duration . " " . trans("days") . ")";
	        }
	        $data['count_product'] = '1';
	        $data['paket'] = $purchased_plan;
	        $data['price'] = $get['total'] / 100;
	        $data['sub_total'] = $get['total'] / 100;
	        $data['total'] = $get['total'] / 100;
	        $data['status'] = $get['status'];
	        $data['bank'] = $get['channel'];
	        $data['va'] = $get['va'];

			$this->return['status'] = true;
    		$this->return['message'] = "Success";
    		$this->return['data'] = $data;

    		$this->response($this->return);
		}
	}
}
?>
