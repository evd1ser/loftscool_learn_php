<?php

namespace App\Models;

use Base\Context;
use Base\Models\BaseModel;
use Base\Models\ModelGetByIds;
use Base\Models\ModelGetByIdTrait;
use Base\Models\ModelGetList;
use Base\Session;

use const Base\CONNECTION_DEFAULT;

class UserModel extends BaseModel
{
    use ModelGetByIdTrait;
    use ModelGetList;
    use ModelGetByIds;

    public    $table      = "users";
    protected $primaryKey = 'id';
    protected $connection = CONNECTION_DEFAULT;

    protected $fillable = ['email','name', 'password', 'info'];//разрешено редактировать только это, остальное запрещено

    //    protected $guarded = ['id']; //запрещено редактировать только это, все остальное разрешено

    public function posts()
    {
        // users.id == posts.user_id
        return $this->hasMany(MessageModel::class, 'user_id', 'id');
    }


    public static function getInstance()
    {
        return self::class;
    }

    protected $fields = [
      'id',
      'name',
      'created_at',
      'email',
      'password'
    ];

    protected $fieldsSaved = [
      'name',
      'email',
      'password'
    ];

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

        try {
            $user = UserModel::where(['email' => $this->email, 'password' => $this->password])->firstOrFail();

            if ($user) {
                $session = Session::instance();
                $session->save((int)$user->id);
                return true;
            }
        } catch (\Exception $e){
            return false;
        }

        return false;
    }

    public function isAdmin()
    {
        $adminIds = explode(',', $_ENV['ADMINS']);
        return in_array($this->id, $adminIds);
    }
}
