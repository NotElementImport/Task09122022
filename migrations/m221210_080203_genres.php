<?php

use yii\db\Migration;

class m221210_080203_genres extends Migration
{
    public function safeUp()
    {
        $this->createTable('genres', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'alias' => $this->string()->notNull()
        ]);

        $this->createIndex(
            'idx-genres_id',
            'genres',
            'id'
        );

        $this->insert('genres', [
            'name' => 'Blues',
            'alias' => 'blues'
        ]);

        $this->insert('genres', [
            'name' => 'Jazz',
            'alias' => 'jazz'
        ]);

        $this->insert('genres', [
            'name' => 'Pop',
            'alias' => 'pop'
        ]);

        $this->insert('genres', [
            'name' => 'Electro',
            'alias' => 'electro'
        ]);

        $this->insert('genres', [
            'name' => 'Techno',
            'alias' => 'techno'
        ]);

        $this->insert('genres', [
            'name' => 'House',
            'alias' => 'house'
        ]);

        $this->insert('genres', [
            'name' => 'Dubstep',
            'alias' => 'dubstep'
        ]);

        $this->insert('genres', [
            'name' => 'Trap',
            'alias' => 'trap'
        ]);

        $this->insert('genres', [
            'name' => 'Hip hop',
            'alias' => 'hip hop'
        ]);

        $this->insert('genres', [
            'name' => 'Rap',
            'alias' => 'rap'
        ]);

        $this->insert('genres', [
            'name' => 'Big room',
            'alias' => 'big room'
        ]);

        $this->insert('genres', [
            'name' => 'Rock',
            'alias' => 'rock'
        ]);

        $this->insert('genres', [
            'name' => 'Metal',
            'alias' => 'metal'
        ]);

        $this->insert('genres', [
            'name' => 'Folk',
            'alias' => 'folk'
        ]);
    }

    public function safeDown()
    {
        $this->dropIndex('idx-genres_id','genres');

        $this->dropTable('genres');
    }
}
