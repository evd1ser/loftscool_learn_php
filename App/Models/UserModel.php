<?php

namespace App\Models;

use Base\Context;
use Base\Models\BaseModel;
use Base\Models\ModelGetByIds;
use Base\Models\ModelGetByIdTrait;
use Base\Models\ModelGetList;
use Base\Session;
use http\Exception;

class UserModel extends BaseModel
{
    use ModelGetByIdTrait;
    use ModelGetList;
    use ModelGetByIds;

    public static function getInstance()
    {
        return self::class;
    }

    protected $fields = [
      'id',
      'name',
      'create_at',
      'email',
      'password'
    ];

    protected $fieldsSaved = [
      'name',
      'email',
      'password'
    ];

    public static function getTable()
    {
        return 'users';
    }

    static public function loginByData($data)
    {
        $user = new self();

        if (strlen($data['password']) < 4) {
            throw new \Exception('password to short');
        }

        $user->email = html_entity_decode(trim($data['email']));
        $user->password = md5($data['password']);

        return $user;
    }

    static public function initByData($data)
    {
        $user = new self();

        if (strlen($data['password']) < 4) {
            throw new \Exception('password to short');
        }

        if ($data['password'] !== $data['password_confirm']) {
            throw new \Exception('password not confirmed');
        }

        $user->name = html_entity_decode(trim($data['name']));
        $user->email = html_entity_decode(trim($data['email']));
        $user->password = md5($data['password']);

        $user->save();
    }

    public function logOut()
    {
        $session = Session::instance();
        $session->destroy();
    }

    public function authorize()
    {
        $db = Context::getInstance()
          ->getDbConnection();
        $select = "SELECT * FROM users WHERE `email` = :email AND password = :password_hash";

        $data = $db->fetchOne($select, __METHOD__, [
          ':email' => $this->email,
          ':password_hash' => $this->password
        ]);

        if ($data) {
            $session = Session::instance();
            $session->save((int)$data['id']);
            return true;
        }

        return false;
    }

    public function isAdmin()
    {
        $adminIds = explode(',', $_ENV['ADMINS']);
        return in_array($this->id, $adminIds);
    }
}
