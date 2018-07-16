<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_post_votes`.
 * Has foreign keys to the tables:
 *
 * - `users`
 * - `posts`
 * - `list_item`
 */
class m180616_213818_create_user_post_votes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_post_votes', [
            'id' => $this->primaryKey(),
            'users_id' => $this->integer()->notNull(),
            'posts_id' => $this->bigInteger()->notNull(),
            'list_item_id' => $this->integer()->notNull(),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // creates index for column `users_id`
        $this->createIndex(
            'idx-user_post_votes-users_id',
            'user_post_votes',
            'users_id'
        );

        // add foreign key for table `users`
        $this->addForeignKey(
            'fk-user_post_votes-users_id',
            'user_post_votes',
            'users_id',
            'users',
            'id',
            'CASCADE'
        );

        // creates index for column `posts_id`
        $this->createIndex(
            'idx-user_post_votes-posts_id',
            'user_post_votes',
            'posts_id'
        );

        // add foreign key for table `posts`
        $this->addForeignKey(
            'fk-user_post_votes-posts_id',
            'user_post_votes',
            'posts_id',
            'posts',
            'id',
            'CASCADE'
        );

        // creates index for column `list_item_id`
        $this->createIndex(
            'idx-user_post_votes-list_item_id',
            'user_post_votes',
            'list_item_id'
        );

        // add foreign key for table `list_item`
        $this->addForeignKey(
            'fk-user_post_votes-list_item_id',
            'user_post_votes',
            'list_item_id',
            'post_lists',
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
            'fk-user_post_votes-users_id',
            'user_post_votes'
        );

        // drops index for column `users_id`
        $this->dropIndex(
            'idx-user_post_votes-users_id',
            'user_post_votes'
        );

        // drops foreign key for table `posts`
        $this->dropForeignKey(
            'fk-user_post_votes-posts_id',
            'user_post_votes'
        );

        // drops index for column `posts_id`
        $this->dropIndex(
            'idx-user_post_votes-posts_id',
            'user_post_votes'
        );

        // drops foreign key for table `list_item`
        $this->dropForeignKey(
            'fk-user_post_votes-list_item_id',
            'user_post_votes'
        );

        // drops index for column `list_item_id`
        $this->dropIndex(
            'idx-user_post_votes-list_item_id',
            'user_post_votes'
        );

        $this->dropTable('user_post_votes');
    }
}
