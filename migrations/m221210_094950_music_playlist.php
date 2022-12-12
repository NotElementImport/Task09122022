<?php

use yii\db\Migration;
use yii\db\Schema;

class m221210_094950_music_playlist extends Migration
{
    public function safeUp()
    {
        $this->createTable('music_playlist', [
            'id' => $this->primaryKey(),
            'songname' => $this->string()->notNull(),
            'songfile' => $this->text()->notNull(),
            'imagefile' => $this->text(),
            'genre' => $this->integer()->notNull(),
            'whoupload' => $this->integer()->notNull()
        ]);

        $this->createIndex(
            'idx-music_playlist_genre',
            'music_playlist',
            'genre'
        );

        $this->addForeignKey(
            'fk-music_playlist_genre',
            'music_playlist',
            'genre',
            'genres',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-music_playlist_whoupload',
            'music_playlist',
            'whoupload'
        );

        $this->addForeignKey(
            'fk-music_playlist_whoupload',
            'music_playlist',
            'whoupload',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-music_playlist_genre', 'music_playlist');
        $this->dropIndex('idx-music_playlist_genre', 'music_playlist');

        // $this->dropForeignKey('fk-music_playlist_whoupload', 'music_playlist');
        // $this->dropIndex('idx-music_playlist_whoupload', 'music_playlist');

        $this->dropTable('music_playlist');
    }
}
