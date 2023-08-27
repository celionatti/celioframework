<?php

namespace App\models;

use App\Core\Database\DbModel;



class UserSessions extends DbModel
{
    public $id, $user_id, $hash;

    public static function tableName(): string
    {
        return 'user_sessions';
    }

    public static function findByUserId($user_id)
    {
        return self::findFirst(
            [
                'conditions' => "user_id = :user_id",
                'bind' => ['user_id' => $user_id]
            ]
        );
    }

    public static function findByHash($hash)
    {
        return self::findFirst(
            [
                'conditions' => "hash= :hash",
                'bind' => ['hash' => $hash]
            ]
        );
    }

}