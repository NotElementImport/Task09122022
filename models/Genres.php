<?php

namespace app\models;

use yii\db\ActiveRecord;

class Genres extends ActiveRecord
{
    public static function tableName()
    {
        return "genres";
    }
}

?>