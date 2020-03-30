<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Auth extends REST_Controller{

	public function __construct(){
		parent::__construct();
		$this->return = array('status' => false, 'message' => 'Something wrong', 'data' => []);

		$this->load->model("api_auth_model");
		$this->load->model("api_user_model");
		$this->load->model("api_email_model");
		$this->load->helper('api_helper');
		$this->load->helper('custom_helper');
	}

	public function login_post(){
		$post = [
			'email' => $this->post('email'),
			'password' => $this->post('password'),
			'device_id' => $this->post('device_id')
		];

		try {
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

	public function forgetpass_post()
	{
		$email = $this->post('email');

		$user = $this->api_user_model->get_user_by_email($email);
		if ($user) {
			$this->api_email_model->send_email_reset_password($user->id);
			$this->return['status'] = true;
			$this->return['message'] = 'success';
		} else {
			$this->return['message'] = "Kami tidak dapat menemukan pengguna dengan alamat email itu!";
		}
		
		$this->response($this->return);
	}

	/**
     * Connect with Facebook
     */
    public function connectfacebook_get()
    {
        $fb_url = "https://www.facebook.com/v3.3/dialog/oauth?client_id=" . $this->api_general_settings->getValueOf('facebook_app_id') . "&redirect_uri=" . base_url() . "api/auth/facebook-callback&scope=email&state=" . generate_unique_id();

        redirect($fb_url);
        exit();
    }

    /**
     * Facebook Callback
     */
    public function facebook_callback_get()
    {
        require_once APPPATH . "third_party/facebook/vendor/autoload.php";

        $fb = new \Facebook\Facebook([
            'app_id' => $this->api_general_settings->getValueOf('facebook_app_id'),
            'app_secret' => $this->api_general_settings->getValueOf('facebook_app_secret'),
            'default_graph_version' => 'v2.10',
        ]);
        try {
            $helper = $fb->getRedirectLoginHelper();
            $permissions = ['email'];
            if (isset($_GET['state'])) {
                $helper->getPersistentDataHandler()->set('state', $_GET['state']);
            }
            $accessToken = $helper->getAccessToken();
            $response = $fb->get('/me?fields=name,email', $accessToken);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $user = $response->getGraphUser();
        $fb_user = new stdClass();
        $fb_user->id = $user->getId();
        $fb_user->email = $user->getEmail();
        $fb_user->name = $user->getName();

        $this->auth_model->login_with_facebook($fb_user);

        if (!empty($this->session->userdata('fb_login_referrer'))) {
            redirect($this->session->userdata('fb_login_referrer'));
        } else {
            redirect(base_url());
        }
    }


    /**
     * Connect with Google
     */
    public function connectgoogle_get()
    {
    	$get = $this->get();

    	$user = $this->api_user_model->get_user_by_email($get['email']);

    	if ($user) {
    		$data = $this->api_user_model->get_user_data($get['email']);
    		$dataUpdate = ['device_id' => $get['device_id']];
    		$this->api_user_model->insert_data($dataUpdate, $user->id);
    	}else{
    		$data = $this->api_auth_model->regis_with_google($get);
    	}

    	$this->return['status'] = true;
    	$this->return['message'] = 'success';
    	$this->return['data'] = $data;

    	$this->response($this->return);
    }

}
?>
