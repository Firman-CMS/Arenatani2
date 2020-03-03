<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Messages extends REST_Controller{

	public function __construct(){
		parent::__construct();
		$this->return = array('status' => false, 'message' => 'Something wrong');

		// $this->load->model("api_auth_model");
		$this->load->model("api_messages_model");
		// $this->load->helper('custom_helper');
		$this->load->helper('api_helper');
	}

	public function send_post(){
		$post = [
			'email' => $this->post('email'),
			'password' => $this->post('password'),
			'username' => remove_special_characters($this->post('username')),
		];

		$this->response($this->return);
	}

	public function list_get(){
		$userId = $this->get('user_id');
		if ($userId) {
			
			$list = [];

			$unread = $this->message_model->get_unread_conversations($userId);
			if ($unread) {
				foreach ($unread as $unreadList) {
					$sender = $this->auth_model->get_user($unreadList->sender_id);
					$unreadList->username = $sender->username;
					$unreadList->avatar = getAvatar($sender);
					$unreadList->is_read = 0;

					array_push($list, $unreadList);
				}
			}
			
			$read = $this->message_model->get_read_conversations($userId);
			if ($read) {
				foreach ($read as $readList) {
					$sender = $this->auth_model->get_user($readList->sender_id);
					$readList->username = $sender->username;
					$readList->avatar = getAvatar($sender);
					$readList->is_read = 1;
					
					array_push($list, $readList);
				}
			}

			$this->return['status'] = true;
			$this->return['message'] = "Success";
			$this->return['data'] = $list;
		}else {
			$this->return['message'] = "User not found";
		}

		$this->response($this->return);
	}

	public function conversation_get(){
		$userId = $this->get('user_id');
		$id = $this->get('id_message');

		$conversation = $this->message_model->get_conversation($id);

		if (($userId != $conversation->sender_id) && ($userId != $conversation->receiver_id)) {
			return $this->response($this->return);
		}

        if ($conversation) {
        	$userSender = $this->auth_model->get_user($conversation->sender_id);
        	$conversation->aktif =timeAgo($userSender->last_seen);
        	$conversation->username = $userSender->username;
        	$conversation->avatar = getAvatar($userSender);

        	$data['header'] = $conversation;
        	$messages = $this->message_model->get_messages($conversation->id);
        	$messageList = [];
        	if ($messages) {
        		foreach ($messages as $listMessages) {
        			if ($userId != $listMessages->deleted_user_id) {
	        			$sender = $this->auth_model->get_user($listMessages->sender_id);
						$listMessages->created = timeAgo($listMessages->created_at);
						$listMessages->username = $sender->username;
						$listMessages->avatar = getAvatar($sender);

	        			if ($userId == $listMessages->sender_id) {
	        				$listMessages->position = 'right';
	        			}elseif ($userId == $listMessages->receiver_id){
	        				$listMessages->position = 'left';
	        			} 
	            	
	            		$messageList[] = $listMessages;
        			}
        		}
        		
        		$data['messages'] = $messageList;
        	}

        	$this->return['status'] = true;
			$this->return['message'] = "Success";
			$this->return['data'] = $data;
        }else{
        	$this->return['message'] = "No Data";
        }

		$this->response($this->return);
	}

	public function addconversation_post(){
		$post = [
			'user_id' => $this->post('user_id'),
			'receiver_id' => $this->post('receiver_id'),
			'id_message' => $this->post('id_message'),
			'body_message' => $this->post('body_message')
		];

		if ($this->api_messages_model->add_message($post)) {
			$this->return['status'] = true;
			$this->return['message'] = "Success";
		}else{
			$this->return['message'] = "Tidak ada pesan";
		}

		$this->response($this->return);
	}

	public function countunread_get(){
		$userId = $this->get('user_id');
		if ($userId) {
			$unreadMessageCount = $this->message_model->get_unread_conversations_count($userId);
			$data['count_notif'] = $unreadMessageCount;

			$this->return['status'] = true;
			$this->return['message'] = "Success";
			$this->return['data'] = $data;
		}else {
			$this->return['message'] = "User not found";
		}

		$this->response($this->return);
	}

	public function delconversation_post()
    {
        $conversation_id = $this->post('id_message');
        if ($this->message_model->delete_conversation($conversation_id)) {
        	$this->return['status'] = true;
			$this->return['message'] = "Success";
        }
        
    }
}
?>
