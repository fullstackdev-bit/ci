<?php namespace App\Models;
  
use CodeIgniter\Model;
use Exception;
  
class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $allowedFields = ['username','password','first_name','last_name','created','updated'];

    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    protected function beforeInsert(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    protected function beforeUpdate(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    private function getUpdatedDataWithHashedPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $plaintextPassword = $data['data']['password'];
            $data['data']['password'] = $this->hashPassword($plaintextPassword);
        }
        return $data;
    }

    private function hashPassword(string $plaintextPassword): string
    {
        return password_hash($plaintextPassword, PASSWORD_BCRYPT);
    }
                                      
    public function findUserByUsername(string $userName)
    {
        $user = $this
            ->asArray()
            ->where(['username' => $userName])
            ->first();

        if (!$user) 
            throw new Exception('User does not exist for specified username');

        return $user;
    }

}