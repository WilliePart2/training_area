<?php

use yii\db\Migration;

/**
 * Handles the creation of table `post_likes`.
 */
class m180604_222859_create_post_likes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post_likes', [
            'id' => $this->primaryKey(),
            'post_id' => $this->bigInteger()->notNull(),
            'users_id' => $this->integer()->notNull()
        ]);
        /** creating index and foreign for post_id column */
        $this->createIndex(
            'idx-post_likes-post_id-posts-id',
            'post_likes',
            'post_id'
        );
        $this->addForeignKey(
            'fk-post_likes-post_id-posts-id',
            'post_likes',
            'post_id',
            'posts',
            'id',
            'CASCADE'
        );
        /** creating index and foreign key for users_id column */
        $this->createIndex(
            'idx-post_likes-users_id-users-id',
            'post_likes',
            'users_id'
        );
        $this->addForeignKey(
            'fk-post_likes-users_id-users-id',
            'post_likes',
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
        $this->dropIndex(
            'idx-post_likes-users_id-users-id',
            'post_likes'
        );
        $this->dropForeignKey(
            'fk-post_likes-users_id-users-id',
            'post_likes'
        );
        $this->dropTable('post_dislikes');
    }
}
