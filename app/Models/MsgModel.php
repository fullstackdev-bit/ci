<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class MsgModel extends Model
{
    protected $table = 'email';
    protected $allowedFields = [
        'subject',
        'message',
        'attachment',
        'parent_id',
        'sender_id',
        'sender_label_id',
        'sender_state',
        'receiver_id',
        'receiver_label_id',
        'receiver_state'
    ];
    protected $updatedField = 'updated_at';

    public function inboxMsg($username)
    {
        
        $msg = $this
            ->asArray()
            ->where(['receiver_id' => $username])
            ->where(['receiver_label_id' => 1])
            ->join('users','users.username = email.sender_id','LEFT')
            ->select("email.*")
            ->select('users.first_name, users.last_name')
            ->orderBy('id', 'DESC')            
            ->findAll();

        if (!$msg) throw new Exception('no messages');

        return $msg;
    }

    public function sentMsg($username)
    {
        $msg = $this
            ->asArray()
            ->where(['sender_id' => $username])
            ->where(['sender_label_id' => 2])
            ->join('users','users.username = email.receiver_id','LEFT')
            ->select("email.*")
            ->select('users.first_name, users.last_name')
            ->orderBy('id', 'DESC')            
            ->findAll();

        if (!$msg) throw new Exception('no messages');

        return $msg;
    }

    public function trashMsg($username)
    {
        $msg = $this
            ->asArray()
            ->where(['receiver_id' => $username])
            ->where(['receiver_label_id' => 4])
            ->orGroupStart()
               ->where(['sender_id' => $username])
               ->where(['sender_label_id' => 4])
            ->groupEnd()
            ->join('users','users.username = email.receiver_id','LEFT')
            ->select("email.*")
            ->select('users.first_name, users.last_name')
            ->orderBy('id', 'DESC')  
            ->findAll();

        if (!$msg) throw new Exception('no messages');

        return $msg;
    }    

    public function draftsMsg($username)
    {
        
        $msg = $this
            ->asArray()
            ->where(['sender_id' => $username])
            ->where(['sender_label_id' => 3])
            ->join('users','users.username = email.receiver_id','LEFT')
            ->select("email.*")
            ->select('users.first_name, users.last_name')
            ->orderBy('id', 'DESC')            
            ->findAll();

        if (!$msg) throw new Exception('no messages');

        return $msg;
    }

    public function findMsgById($id)
    {        
        $msg = $this
            ->asArray()
            ->where(['id' => $id])
            ->join('users','users.username = email.sender_id','LEFT')
            ->select("email.*")
            ->select('users.first_name, users.last_name')
            ->first();

        if (!$msg) throw new Exception('Could not find message for specified ID');

        return $msg;
    }
}