<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_follow`.
 */
class m180605_183802_create_user_follow_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_follow', [
            'id' => $this->primaryKey(),
            'follower_id' => $this->integer()->notNull(),
            'followed_id' => $this->integer()->notNull(),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ]);
        $this->createIndex(
            'idx-user_follow-follower_id-users-id',
            'user_follow',
            'follower_id'
        );
        $this->addForeignKey(
            'fk-user_follow-follower_id-users-id',
            'user_follow',
            'follower_id',
            'users',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-user_follow-followed_id-users-id',
            'user_follow',
            'followed_id'
        );
        $this->addForeignKey(
            'idx->user_follow-followed_id-users-id',
            'user_follow',
            'followed_id',
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
        $this->dropTable('user_follow');
    }
}
