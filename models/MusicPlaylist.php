<?php

namespace app\models;

use yii\db\ActiveRecord;

class MusicPlaylist extends ActiveRecord
{
    public static function tableName()
    {
        return "music_playlist";
    }
}

?>