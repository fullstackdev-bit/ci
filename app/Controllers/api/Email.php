<?php

namespace App\Controllers\api;
use App\Controllers\BaseController;
use App\Models\MsgModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Exception;
use ReflectionException;

class Email extends BaseController
{
    /**
     * Get all received messages
     * @return Response
     */
    public function inbox()
    {
        //return $this->respond(['email' => '123'], 200);        
        $input = json_decode($this->request->getBody(), true);
        if(isset($input['username'])){
            $username = $input['username'];        
            try {
                $model = new MsgModel();
                return $this->getResponse(
                    [
                        'message' => 'Messages retrieved successfully',
                        'msgs' => $model->inboxMsg($username)
                    ]
                );

            } catch (Exception $e) {
                return $this->getResponse(
                    [
                        'message' => 'No messages for'. $username
                    ],
                    ResponseInterface::HTTP_NOT_FOUND
                );
            }
        }else{
            return $this
                ->getResponse(
                    [
                        'message' => "No username"
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
        
    }

    public function sent()
    {
        //return $this->respond(['email' => '123'], 200);        
        $input = json_decode($this->request->getBody(), true);
        if(isset($input['username'])){
            $username = $input['username'];        
            try {
                $model = new MsgModel();
                return $this->getResponse(
                    [
                        'message' => 'Messages retrieved successfully',
                        'msgs' => $model->sentMsg($username)
                    ]
                );

            } catch (Exception $e) {
                return $this->getResponse(
                    [
                        'message' => 'No messages for'. $username
                    ],
                    ResponseInterface::HTTP_NOT_FOUND
                );
            }
        }else{
            return $this
                ->getResponse(
                    [
                        'message' => "No username"
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
        
    }

    public function readMsg()
    {
        //return $this->respond(['email' => '123'], 200);        
        $input = json_decode($this->request->getBody(), true);
        if(isset($input['id'])){
            $id =hexdec($input['id']) - 1000000005;   

            try {
                $model = new MsgModel();
                return $this->getResponse(
                    [
                        'message' => 'Messages retrieved successfully',
                        'msgs' => $model->findMsgById($id)
                    ]
                );

            } catch (Exception $e) {
                return $this->getResponse(
                    [
                        'message' => 'No messages for'. $id
                    ],
                    ResponseInterface::HTTP_NOT_FOUND
                );
            }
        }else{
            return $this
                ->getResponse(
                    [
                        'message' => "No username"
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
        
    }
    /**
     * save a new message
     * @return Response
     * @throws ReflectionException
     */
    public function savemsg()
    {
        $rules = [
            'sender_id' => 'required',            
            'receiver_id' => 'required'
        ];

        $input = json_decode($this->request->getBody(), true);
        if (!$this->validateRequest($input, $rules)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
        
        $msgModel = new MsgModel();
        $save = $msgModel->insert($input);

        return $this->getResponse(
            [
                'message' => 'Message added successfully',
                'msg' => $save
            ]
        );

    }

    public function updateState(){
        
        $msgModel = new MsgModel();
        $input = json_decode($this->request->getBody(), true);
        if(isset($input['id'])){
            $id = hexdec($input['id']) - 1000000005;            
            if(isset($input['inboxRead'])){
                $msgModel->set('receiver_state',$input['inboxRead'])
                ->where('id',$id)
                ->update();
            } 
            if(isset($input['sentRead'])){
                $msgModel->set('sender_state',$input['sentRead'])
                ->where('id',$id)
                ->update();
            } 
            if(isset($input['inboxRemove'])){
                $msgModel->set('receiver_label_id',4)
                ->where('id',$id)
                ->update();
            } 
            if(isset($input['sentRemove'])){
                $msgModel->set('sender_label_id',4)
                ->where('id',$id)
                ->update();
            } 
            return $this->getResponse(
                [
                    'message' => 'Message updated successfully'                    
                ]
            );
        }else{
            return $this
                ->getResponse(
                    [
                        'message' => "No message ID"
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }

    }

    public function trash()
    {            
        $input = json_decode($this->request->getBody(), true);
        if(isset($input['username'])){
            $username = $input['username'];        
            try {
                $model = new MsgModel();
                return $this->getResponse(
                    [
                        'message' => 'Messages retrieved successfully',
                        'msgs' => $model->trashMsg($username)
                    ]
                );

            } catch (Exception $e) {
                return $this->getResponse(
                    [
                        'message' => 'No messages for'. $username
                    ],
                    ResponseInterface::HTTP_NOT_FOUND
                );
            }
        }else{
            return $this
                ->getResponse(
                    [
                        'message' => "No username"
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
        
    }

    public function drafts()
    {            
        $input = json_decode($this->request->getBody(), true);
        if(isset($input['username'])){
            $username = $input['username'];        
            try {
                $model = new MsgModel();
                return $this->getResponse(
                    [
                        'message' => 'Messages retrieved successfully',
                        'msgs' => $model->draftsMsg($username)
                    ]
                );

            } catch (Exception $e) {
                return $this->getResponse(
                    [
                        'message' => 'No messages for'. $username
                    ],
                    ResponseInterface::HTTP_NOT_FOUND
                );
            }
        }else{
            return $this
                ->getResponse(
                    [
                        'message' => "No username"
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
        
    }
    
}