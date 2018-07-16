<?php

use yii\db\Migration;

/**
 * Handles the creation of table `chat_room_members`.
 * Has foreign keys to the tables:
 *
 * - `chat_room`
 * - `users`
 */
class m180620_185624_create_chat_room_members_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('chat_room_members', [
            'id' => $this->bigPrimaryKey(),
            'chat_room_id' => $this->bigInteger()->notNull(),
            'member_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `chat_room_id`
        $this->createIndex(
            'idx-chat_room_members-chat_room_id',
            'chat_room_members',
            'chat_room_id'
        );

        // add foreign key for table `chat_room`
        $this->addForeignKey(
            'fk-chat_room_members-chat_room_id',
            'chat_room_members',
            'chat_room_id',
            'chat_room',
            'id',
            'CASCADE'
        );

        // creates index for column `member_id`
        $this->createIndex(
            'idx-chat_room_members-member_id',
            'chat_room_members',
            'member_id'
        );

        // add foreign key for table `users`
        $this->addForeignKey(
            'fk-chat_room_members-member_id',
            'chat_room_members',
            'member_id',
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
        // drops foreign key for table `chat_room`
        $this->dropForeignKey(
            'fk-chat_room_members-chat_room_id',
            'chat_room_members'
        );

        // drops index for column `chat_room_id`
        $this->dropIndex(
            'idx-chat_room_members-chat_room_id',
            'chat_room_members'
        );

        // drops foreign key for table `users`
        $this->dropForeignKey(
            'fk-chat_room_members-member_id',
            'chat_room_members'
        );

        // drops index for column `member_id`
        $this->dropIndex(
            'idx-chat_room_members-member_id',
            'chat_room_members'
        );

        $this->dropTable('chat_room_members');
    }
}
