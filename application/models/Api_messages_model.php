<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_messages_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        
    }


    public function getConversations($userId)
    {
        $this->db->select('conversations.id,conversations.sender_id,conversations.receiver_id, conversations.subject, conversation_messages.message, conversations.created_at, conversation_messages.is_read');
        $this->db->from('conversations');
        $this->db->join('conversation_messages','conversation_messages.conversation_id=conversations.id');
        $this->db->where('conversations.receiver_id', $userId);
        $this->db->where('conversation_messages.deleted_user_id !=', $userId);
        $this->db->order_by('conversations.id', 'DESC');
        $query=$this->db->get();
        $data=$query->result();

        return $data;
    }

    public function add_message($data)
    {
        $conversation_id = clean_number($data['id_message']);
        $datas = array(
            'conversation_id' => $conversation_id,
            'sender_id' => $data['user_id'],
            'receiver_id' => $data['user_id'],
            'message' => $data['body_message'],
            'is_read' => 0,
            'deleted_user_id' => 0,
            'created_at' => date("Y-m-d H:i:s")
        );
        if (!empty($data['body_message'])) {
            return $this->db->insert('conversation_messages', $datas);
        }
        return false;
    }

}