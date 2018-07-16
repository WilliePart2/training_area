<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_post_views`.
 * Has foreign keys to the tables:
 *
 * - `users`
 * - `post`
 */
class m180618_211555_create_user_post_views_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_post_views', [
            'id' => $this->primaryKey(),
            'users_id' => $this->integer()->notNull(),
            'post_id' => $this->bigInteger()->notNull(),
        ]);

        // creates index for column `users_id`
        $this->createIndex(
            'idx-user_post_views-users_id',
            'user_post_views',
            'users_id'
        );

        // add foreign key for table `users`
        $this->addForeignKey(
            'fk-user_post_views-users_id',
            'user_post_views',
            'users_id',
            'users',
            'id',
            'CASCADE'
        );

        // creates index for column `post_id`
        $this->createIndex(
            'idx-user_post_views-post_id',
            'user_post_views',
            'post_id'
        );

        // add foreign key for table `post`
        $this->addForeignKey(
            'fk-user_post_views-post_id',
            'user_post_views',
            'post_id',
            'posts',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `users`
        $this->dropForeignKey(
            'fk-user_post_views-users_id',
            'user_post_views'
        );

        // drops index for column `users_id`
        $this->dropIndex(
            'idx-user_post_views-users_id',
            'user_post_views'
        );

        // drops foreign key for table `post`
        $this->dropForeignKey(
            'fk-user_post_views-post_id',
            'user_post_views'
        );

        // drops index for column `post_id`
        $this->dropIndex(
            'idx-user_post_views-post_id',
            'user_post_views'
        );

        $this->dropTable('user_post_views');
    }
}
