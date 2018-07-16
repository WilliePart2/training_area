<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_rating`.
 */
class m180605_123252_create_user_rating_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_rating', [
            'id' => $this->primaryKey(),
            'vote_owner_id' => $this->integer()->notNull(),
            'users_id' => $this->integer()->notNull(),
            'value' => $this->tinyInteger()->notNull()->defaultValue(0)
        ]);
        /** adding index and foreign key for vote_owner_id */
        $this->createIndex(
            'idx-user_rating-vote_owner_id-id',
            'user_rating',
            'vote_owner_id'
        );
        $this->addForeignKey(
            'fk-user_rating-vote_owner_id-users-id',
            'user_rating',
            'vote_owner_id',
            'users',
            'id',
            'CASCADE'
        );
        /** adding index and foreign key for users_id */
        $this->createIndex(
            'idx-user_rating-users_id-users-id',
            'user_rating',
            'users_id'
        );
        $this->addForeignKey(
            'fk-user_rating-users_id-users-id',
            'user_rating',
            'users_id',
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
        $this->dropTable('user_rating');
    }
}
