<?php

use yii\db\Migration;

/**
 * Handles the creation of table `chat_room`.
 * Has foreign keys to the tables:
 *
 * - `creator`
 */
class m180620_185220_create_chat_room_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('chat_room', [
            'id' => $this->bigPrimaryKey(),
            'creator_id' => $this->integer()->notNull(),
            'valid' => $this->tinyInteger()->notNull()->defaultValue(0),
            'topic' => $this->string(255)->notNull()->defaultValue('')
        ]);

        // creates index for column `creator_id`
        $this->createIndex(
            'idx-chat_room-creator_id',
            'chat_room',
            'creator_id'
        );

        // add foreign key for table `creator`
        $this->addForeignKey(
            'fk-chat_room-creator_id',
            'chat_room',
            'creator_id',
            'users',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `creator`
        $this->dropForeignKey(
            'fk-chat_room-creator_id',
            'chat_room'
        );

        // drops index for column `creator_id`
        $this->dropIndex(
            'idx-chat_room-creator_id',
            'chat_room'
        );

        $this->dropTable('chat_room');
    }
}
