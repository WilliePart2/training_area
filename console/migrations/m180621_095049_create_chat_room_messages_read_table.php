<?php

use yii\db\Migration;

/**
 * Handles the creation of table `chat_room_messages_read`.
 * Has foreign keys to the tables:
 *
 * - `chat_room`
 * - `member`
 * - `chat_room_messages`
 */
class m180621_095049_create_chat_room_messages_read_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('chat_room_messages_read', [
            'id' => $this->bigPrimaryKey(),
            'member_id' => $this->integer()->notNull(),
            'message_id' => $this->bigInteger()->notNull(),
        ]);

        // creates index for column `member_id`
        $this->createIndex(
            'idx-chat_room_messages_read-member_id',
            'chat_room_messages_read',
            'member_id'
        );

        // add foreign key for table `member`
        $this->addForeignKey(
            'fk-chat_room_messages_read-member_id',
            'chat_room_messages_read',
            'member_id',
            'users',
            'id',
            'CASCADE'
        );

        // creates index for column `message_id`
        $this->createIndex(
            'idx-chat_room_messages_read-message_id',
            'chat_room_messages_read',
            'message_id'
        );

        // add foreign key for table `chat_room_messages`
        $this->addForeignKey(
            'fk-chat_room_messages_read-message_id',
            'chat_room_messages_read',
            'message_id',
            'chat_room_messages',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `chat_room`
        $this->dropForeignKey(
            'fk-chat_room_messages_read-chat_room_id',
            'chat_room_messages_read'
        );

        // drops index for column `chat_room_id`
        $this->dropIndex(
            'idx-chat_room_messages_read-chat_room_id',
            'chat_room_messages_read'
        );

        // drops foreign key for table `member`
        $this->dropForeignKey(
            'fk-chat_room_messages_read-member_id',
            'chat_room_messages_read'
        );

        // drops index for column `member_id`
        $this->dropIndex(
            'idx-chat_room_messages_read-member_id',
            'chat_room_messages_read'
        );

        // drops foreign key for table `chat_room_messages`
        $this->dropForeignKey(
            'fk-chat_room_messages_read-message_id',
            'chat_room_messages_read'
        );

        // drops index for column `message_id`
        $this->dropIndex(
            'idx-chat_room_messages_read-message_id',
            'chat_room_messages_read'
        );

        $this->dropTable('chat_room_messages_read');
    }
}
