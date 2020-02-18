<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class User extends REST_Controller{

	public function __construct(){
		parent::__construct();
		$this->return = array('status' => false, 'message' => 'Something wrong');

		$this->load->model("api_auth_model");
		// $this->load->model("api_user_model");
	}

	public function register_post(){
		$post = [
			'email' => $this->post('email'),
			'password' => $this->post('password'),
			'username' => remove_special_characters($this->post('username')),
		];

		if (!$this->auth_model->is_unique_email($post['email'])) {
			$this->return['message'] = "Email ini sudah di pakai !";

		}elseif (!$this->auth_model->is_unique_username($post['username'])) {
			$this->return['message'] = "Nama pengguna telah digunakan !";

		}else{

			$user = $this->api_auth_model->register($post);
			if ($user) {
				$this->return['status'] = true;
				$this->return['message'] = "Success";
			}
		}

		$this->response($this->return);
	}
}
?>
