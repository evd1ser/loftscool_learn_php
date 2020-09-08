<?php

namespace App\Models;

use Base\Models\BaseModel;
use Base\Models\ModelGetByIds;
use Base\Models\ModelGetByIdTrait;
use Base\Models\ModelGetList;
use const Base\CONNECTION_DEFAULT;

class MessageModel extends BaseModel
{
    use ModelGetByIdTrait;
    use ModelGetList;
    use ModelGetByIds;


    public    $table      = "messages";
    protected $primaryKey = 'id';
    protected $connection = CONNECTION_DEFAULT;

    protected $fillable = ['message', 'user_id'];//разрешено редактировать только это, остальное запрещено


    public static function getInstance()
    {
        return self::class;
    }


    public function getUser()
    {
        return $this->belongsTo(UserModel::class);
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
