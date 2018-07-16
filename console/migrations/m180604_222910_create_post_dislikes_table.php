<?php

use yii\db\Migration;

/**
 * Handles the creation of table `post_dislikes`.
 */
class m180604_222910_create_post_dislikes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post_dislikes', [
            'id' => $this->primaryKey(),
            'post_id' => $this->bigInteger()->notNull(),
            'users_id' => $this->integer()->notNull()
        ]);
        /** creating index and foreign for post_id column */
        $this->createIndex(
            'idx-post_dislikes-post_id-posts-id',
            'post_dislikes',
            'post_id'
        );
        $this->addForeignKey(
            'fk-post_dislikes-post_id-posts-id',
            'post_dislikes',
            'post_id',
            'posts',
            'id',
            'CASCADE'
        );
        /** creating index and foreign key for users_id column */
        $this->createIndex(
            'idx-post_dislikes-users_id-users-id',
            'post_dislikes',
            'users_id'
        );
        $this->addForeignKey(
            'fk-post_dislikes-users_id-users-id',
            'post_dislikes',
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
    {$this->dropIndex(
        'idx-post_dislikes-users_id-users-id',
        'post_dislikes'
    );
        $this->dropForeignKey(
            'fk-post_dislikes-users_id-users-id',
            'post_dislikes'
        );
        $this->dropTable('post_dislikes');
    }
}
