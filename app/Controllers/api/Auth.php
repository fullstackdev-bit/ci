<?php

namespace App\Controllers\api;
use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Exception;
use ReflectionException;

class Auth extends BaseController
{
    /**
     * Register a new user
     * @return Response
     * @throws ReflectionException
     */
    public function register()
    {
        $rules = [
            'username' => 'required|is_unique[users.username]|max_length[50]',            
            'password' => 'required|min_length[3]|max_length[255]',
            'first_name' => 'required',
            'last_name' => 'required'
        ];

        //$input = $this->getRequestInput($this->request);
        $input = json_decode($this->request->getBody(), true);
        
        if (!$this->validateRequest($input, $rules)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }

        $userModel = new UserModel();
        $userModel->save($input);

        return $this
            ->getJWTForUser(
                $input['username'],
                ResponseInterface::HTTP_CREATED
            );

    }

    /**
     * Authenticate Existing User
     * @return Response
     */
    public function login()
    {
        $rules = [
            'username' => 'required|max_length[50]',
            'password' => 'required|min_length[3]|max_length[255]|validateUser[username, password]'
        ];

        $errors = [
            'password' => [
                'validateUser' => 'Invalid login credentials provided'
            ]
        ];

        // $input = $this->getRequestInput($this->request);
        $input = json_decode($this->request->getBody(), true);


        if (!$this->validateRequest($input, $rules, $errors)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
       return $this->getJWTForUser($input['username']);
    }

    public function me() {
        
    }

    private function getJWTForUser(
        string $userName,
        int $responseCode = ResponseInterface::HTTP_OK
    )    
    {
        try {
            $model = new UserModel();
            $user = $model->findUserByUsername($userName);
            unset($user['password']);

            helper('jwt');

            return $this
                ->getResponse(
                    [
                        'message' => 'User authenticated successfully',
                        'user' => $user,
                        'access_token' => getSignedJWTForUser($userName)
                    ]
                );
        } catch (Exception $exception) {
            return $this
                ->getResponse(
                    [
                        'error' => $exception->getMessage(),
                    ],
                    $responseCode
                );
        }
    }
}