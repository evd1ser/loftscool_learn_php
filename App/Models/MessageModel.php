<?php

namespace App\Models;

use Base\Models\BaseModel;
use Base\Models\ModelGetByIds;
use Base\Models\ModelGetByIdTrait;
use Base\Models\ModelGetList;

class MessageModel extends BaseModel
{
    use ModelGetByIdTrait;
    use ModelGetList;
    use ModelGetByIds;

    public static function getInstance()
    {
        return self::class;
    }

    protected $fields      = [
      'id',
      'message',
      'create_at',
      'user_id',
    ];
    protected $fieldsSaved = [
      'message',
      'user_id',
    ];

    public static function getTable()
    {
        return 'messages';
    }

    public function getUser()
    {
        return UserModel::getById(__METHOD__, $this->user_id);
    }

    static public function initByData($data)
    {
        $message = new self();

        $message->message = html_entity_decode(trim($data['message']));
        $message->user_id = $data['user_id'];
        $message->save();


        return $message;
    }

    public function getImage()
    {
        $imgPatch = '/images/' . $this->id . '.png';
        $isExist = file_exists(__DIR__ . '/../../public' . $imgPatch);

        if ($isExist) {
            return $imgPatch;
        }
        return false;

    }
}
