<?php
if (defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Auth extends REST_Controller{

	public function __construct(){
		parent::__construct();
		$this->return = array('status' => false, 'message' => 'Something wrong', 'data' => []);

		$this->load->model("api_auth_model");
		$this->load->model("api_user_model");

		// $this->load->model('authApi');
	}

	public function login_post(){
		$post = [
			'email' => $this->post('email'),
			'password' => $this->post('password'),
			'device_id' => $this->post('device_id')
		];

		try {
			//cheklogin belum lengkap
			$checkLogin = $this->api_auth_model->login($post);

			if ($checkLogin) {
				$data = $this->api_user_model->get_user_data($post['email']);
				$this->return['status'] = true;
				$this->return['message'] = 'success';
				$this->return['data'] = $data;
			}else{
				$this->return['message'] = 'Email atau password salah!';
			}
		} catch (Exception $e) {
			$this->return['message'] = $e->getMessage();
		}


		$this->response($this->return);
	}

}
?>
