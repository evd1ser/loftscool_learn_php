<?php

namespace App\Controllers;

use App\Models\MessageModel;
use App\Models\UserModel;
use Base\ControllerAbstract;
use Base\View;
use mysql_xdevapi\Exception;

class BlogController extends ControllerAbstract
{
    public function index()
    {
        if (!$this->USER) {
            $this->redirect('/login');
        }

        $messages = MessageModel::limit(20)->latest()->get();

        $view = new View('blog.index');
        $view->messages = $messages;
        $view->user = $this->USER;

        return $view;
    }

    public function createMessage()
    {
        if (!$this->USER) {
            $this->redirect('/login');
        }

        $message = MessageModel::create([
          'message' => $this->p('message'),
          'user_id' => $this->USER->id,
        ]);

        var_dump($_FILES["image"]);

        if (isset($_FILES["image"]) && !empty($_FILES["image"])) {

            $img_file = $_FILES["image"]["name"];
            $folderName = __DIR__ . "/../../public/images/";
            $validExt = array("png");

            if ($img_file == "") {
            } elseif ($_FILES["image"]["size"] <= 0) {
                $msg = errorMessage("Image is not proper.");
            } else {
                $ext = strtolower(end(explode(".", $img_file)));

                if (!in_array($ext, $validExt)) {
                    $msg = errorMessage("Not a valid image file");
                } else {
                    $filePath = $folderName . $message->id . "." . $ext;
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $filePath)) {

                    }
                }
            }
        }


        $this->redirect(' / ');
    }

    public function apiGet()
    {
        $user_id = (int)$this->p('user_id');

        if ($user_id <= 0) {
            throw new \Exception('user_id incorrect');
        }
        function flatten(array $array)
        {
            $return = array();
            array_walk_recursive($array, function ($a) use (&$return) {
                $return[] = $a;
            });
            return $return;
        }


        $messages = MessageModel::getList(__METHOD__, 20, [['user_id', '=', $user_id]], 'created_at');

        $messages = flatten($messages);

        $this->_noRender = true;

        header('Content-type: application/json');
        return json_encode($messages);
    }
}
