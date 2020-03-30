<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'third_party/image-resize/ImageResize.php';
require APPPATH . 'third_party/image-resize/ImageResizeException.php';

class Setting extends REST_Controller{

	public function __construct(){
		parent::__construct();
		$this->return = array('status' => false, 'message' => 'Something wrong');

		$this->load->model("auth_model");
		$this->load->model("api_general_settings");
		$this->load->model("api_profile_model");
		$this->load->model("api_upload_model");
		$this->load->helper('api_helper');

	}

	public function profiles($userId)
	{
		return $user = $this->auth_model->get_user($userId);
	}

	public function profile_get()
	{
		$userData = $this->profiles($this->get('user_id'));
		if ($userData) {
			$user = [
				'id' => $userData->id,
				'username' => $userData->username,
				'avatar' => getAvatar($userData),
				'email' => $userData->email,
				'email_status' => $userData->email_status,
				'slug' => $userData->slug,
				'send_email_new_message' => $userData->send_email_new_message,
			];

			$this->return['status'] = true;
            $this->return['message'] = "Success";
            $this->return['data'] = $user;
		}

		$this->response($this->return);
	}

	public function profile_post()
	{
		$data = array(
            'user_id' => $this->post('user_id'),
            'username' => $this->post('username'),
            'email' => $this->post('email'),
            'slug' => $this->post('slug'),
            'send_email_new_message' => $this->post('send_email_new_message')
        );

        //is email unique
        if (!$this->auth_model->is_unique_email($data["email"], $data['user_id'])) {
            $this->return['message'] = trans("msg_email_unique_error");
            return $this->response($this->return);
        }
        //is username unique
        if (!$this->auth_model->is_unique_username($data["username"], $data['user_id'])) {
            $this->return['message'] = trans("msg_username_unique_error");
            return $this->response($this->return);
        }
        //is slug unique
        if ($this->auth_model->check_is_slug_unique($data["slug"], $data['user_id'])) {
            $this->return['message'] = trans("msg_slug_unique_error");
            return $this->response($this->return);
        }
        
        $user = $this->auth_model->get_user($data['user_id']);
        if ($this->api_profile_model->update_profile($data)) {

			if ($_FILES) {
				$uploadAvatar = $this->uploadImg($user);
			}

            //check email changed
            if ($data['email'] != $user->email) {
	            if (!$this->api_profile_model->check_email_updated($user->email,$data['user_id'])) {
	            	$this->return['message'] = "Email tidak dapat disimpan";
	                $this->response($this->return);
	            }
            }

            $this->return['status'] = true;
            $this->return['message'] = "Success";
        }
        return $this->response($this->return);
	}

	public function contactinfo_get()
	{
		$userData = $this->profiles($this->get('user_id'));
		if ($userData) {
			$user = [
				'id' => $userData->id,
				'username' => $userData->username,
				'country_id' => $userData->country_id,
				'state_id' => $userData->state_id,
				'city_id' => $userData->city_id,
				'address' => $userData->address,
				'zip_code' => $userData->zip_code,
				'phone_number' => $userData->phone_number,
				'show_email' => $userData->show_email,
				'show_phone' => $userData->show_phone,
				'show_location' => $userData->show_location,
			];

			$this->return['status'] = true;
            $this->return['message'] = "Success";
            $this->return['data'] = $user;
		}

		$this->response($this->return);
	}

	public function contactinfo_post()
	{
		$data = array(
            'user_id' => $this->post('user_id'),
            'country_id' => $this->post('country_id'),
            'state_id' => $this->post('state_id'),
            'city_id' => $this->post('city_id'),
            'address' => $this->post('address'),
            'zip_code' => $this->post('zip_code'),
            'phone_number' => $this->post('phone_number'),
            'show_email' => $this->post('show_email'),
            'show_phone' => $this->post('show_phone'),
            'show_location' => $this->post('show_location')
        );

        $update = $this->api_profile_model->update_contact_informations($data);

        if ($update) {
         	$this->return['status'] = true;
            $this->return['message'] = "Success";
        }

        $this->response($this->return);
	}

	public function uploadImg($user)
	{
		if(!empty($_FILES['avatar']['name'])){
			$urlAvatar = explode("/" , $user->avatar);
			$currentAvatar = end($urlAvatar);
			
			if ($currentAvatar != $_FILES['avatar']['name']) {
				$_FILES['file']['name'] = $_FILES['avatar']['name'];
				$_FILES['file']['type'] = $_FILES['avatar']['type'];
				$_FILES['file']['tmp_name'] = $_FILES['avatar']['tmp_name'];
				$_FILES['file']['error'] = $_FILES['avatar']['error'];
				$_FILES['file']['size'] = $_FILES['avatar']['size'];

				$config['upload_path'] = 'uploads/temp/'; 
				$config['allowed_types'] = 'jpg|jpeg|png|gif';
				$config['max_size'] = '5000';
				$config['file_name'] = 'temp_product'. generate_unique_id();;

				$this->load->library('upload',$config);
				if($this->upload->do_upload('file')){
					$uploadData = $this->upload->data();
					
					$temp_path = $uploadData['full_path'];
					
					$datas['avatar'] = $this->api_upload_model->avatar_upload($temp_path);
					$datas['user_id'] = $user->id;
					$this->api_profile_model->update_profile($datas);

					$this->api_upload_model->delete_temp_image($temp_path);

				}
			}
		}
	}
}
?>
