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
            'receiver_id' => $data['receiver_id'],
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

    public function add_conversation($data)
    {
        $datas = [
            'sender_id' => $data['user_id'],
            'receiver_id' => $data['receiver_id'],
            'subject' => $data['subject'],
            'created_at' => date("Y-m-d H:i:s")
        ];
        //check conversation exists
        $this->db->where('sender_id', $datas['sender_id']);
        $this->db->where('receiver_id', $datas['receiver_id']);
        $this->db->where('subject', $datas['subject']);
        $query = $this->db->get('conversations');
        $row = $query->row();

        if (!empty($row)) {
            return $row->id;
        } else {
            if ($this->db->insert('conversations', $datas)) {
                return $this->db->insert_id();
            } else {
                return false;
            }
        }
    }

    public function delete_conversation($id,$userId)
    {
        $id = clean_number($id);
        $conversation = $this->get_conversation($id);
        if ($conversation) {
            $messages = $this->get_messages($conversation->id);
            if ($messages) {
                foreach ($messages as $message) {
                    if ($message->sender_id == $userId || $message->receiver_id == $userId) {
                        if ($message->deleted_user_id == 0) {
                            $data = array(
                                'deleted_user_id' => $userId
                            );
                            $this->db->where('id', $message->id);
                            $this->db->update('conversation_messages', $data);
                        } else {
                            $this->db->where('id', $message->id);
                            $this->db->delete('conversation_messages');
                        }
                        return true;
                    }else{
                        return false;
                    }
                }
            }else {
                $this->db->where('id', $conversation->id);
                $this->db->delete('conversations');
                return true;
            }
        }else {
            return false;
        }
    }

    public function get_conversation($id)
    {
        $id = clean_number($id);
        $this->db->where('id', $id);
        $query = $this->db->get('conversations');
        return $query->row();
    }

    public function get_messages($conversation_id)
    {
        $conversation_id = clean_number($conversation_id);
        $this->db->where('conversation_id', $conversation_id);
        $query = $this->db->get('conversation_messages');
        return $query->result();
    }

}