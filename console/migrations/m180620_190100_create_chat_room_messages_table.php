<?php

use yii\db\Migration;

/**
 * Handles the creation of table `chat_room_messages`.
 * Has foreign keys to the tables:
 *
 * - `sender`
 * - `room_receiver`
 */
class m180620_190100_create_chat_room_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('chat_room_messages', [
            'id' => $this->bigPrimaryKey(),
            'sender_id' => $this->integer()->notNull(),
            'room_receiver_id' => $this->bigInteger()->notNull(),
            'message' => $this->text()->notNull(),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // creates index for column `sender_id`
        $this->createIndex(
            'idx-chat_room_messages-sender_id',
            'chat_room_messages',
            'sender_id'
        );

        // add foreign key for table `sender`
        $this->addForeignKey(
            'fk-chat_room_messages-sender_id',
            'chat_room_messages',
            'sender_id',
            'users',
            'id',
            'CASCADE'
        );

        // creates index for column `room_receiver_id`
        $this->createIndex(
            'idx-chat_room_messages-room_receiver_id',
            'chat_room_messages',
            'room_receiver_id'
        );

        // add foreign key for table `room_receiver`
        $this->addForeignKey(
            'fk-chat_room_messages-room_receiver_id',
            'chat_room_messages',
            'room_receiver_id',
            'chat_room',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `sender`
        $this->dropForeignKey(
            'fk-chat_room_messages-sender_id',
            'chat_room_messages'
        );

        // drops index for column `sender_id`
        $this->dropIndex(
            'idx-chat_room_messages-sender_id',
            'chat_room_messages'
        );

        // drops foreign key for table `room_receiver`
        $this->dropForeignKey(
            'fk-chat_room_messages-room_receiver_id',
            'chat_room_messages'
        );

        // drops index for column `room_receiver_id`
        $this->dropIndex(
            'idx-chat_room_messages-room_receiver_id',
            'chat_room_messages'
        );

        $this->dropTable('chat_room_messages');
    }
}
