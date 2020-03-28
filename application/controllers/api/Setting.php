<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Setting extends REST_Controller{

	public function __construct(){
		parent::__construct();
		$this->return = array('status' => false, 'message' => 'Something wrong');

		$this->load->model("auth_model");
		// $this->load->model("api_general_settings");
		// $this->load->model("api_category_model");
		$this->load->model("api_profile_model");
		// $this->load->model("api_file_model");
		// $this->load->helper('api_helper');
		// $this->product_per_page = 15;

	}

	public function contactinfo_get()
	{
		$userId = $this->get('user_id');
		$user = $this->auth_model->get_user($userId);
		if ($user) {
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
}
?>
