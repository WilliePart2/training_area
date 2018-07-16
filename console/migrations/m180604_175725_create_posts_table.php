<?php

use yii\db\Migration;

/**
 * Handles the creation of table `posts`.
 */
class m180604_175725_create_posts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('posts', [
            'id' => $this->bigPrimaryKey(),
            'owner_id' => $this->integer()->notNull(),
            'views' => $this->integer()->notNull()->defaultValue(0),
            'type' => $this->string(100)->notNull()->defaultValue(''),
            'name' => $this->string(255)->notNull()->defaultValue(''),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        /** creating index and foreign key for owner_id */
        $this->createIndex(
            'idx-posts-owner_id-users-id',
            'posts',
            'owner_id'
        );
        $this->addForeignKey(
            'fk-posts-owner_id-users-id',
            'posts',
            'owner_id',
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
            'idx-posts-owner_id-users-id',
            'posts'
        );
        $this->dropForeignKey(
            'fk-posts-owner_id-users-id',
            'posts'
        );
        $this->dropTable('posts');
    }
}
