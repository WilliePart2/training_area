<?php

use yii\db\Migration;

/**
 * Handles the creation of table `post_actions`.
 * Has foreign keys to the tables:
 *
 * - `initiator` - relation to `users` table
 * - `target` - relation to `posts` table
 */
class m180627_173725_create_post_actions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post_actions', [
            'id' => $this->primaryKey(),
            'initiator' => $this->integer()->notNull(),
            'target_id' => $this->bigInteger()->notNull(),
            'action_type' => $this->string(255)->notNull()->defaultValue(''),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // creates index for column `initiator`
        $this->createIndex(
            'idx-post_actions-initiator',
            'post_actions',
            'initiator'
        );

        // add foreign key for table `initiator`
        $this->addForeignKey(
            'fk-post_actions-initiator',
            'post_actions',
            'initiator',
            'users',
            'id',
            'CASCADE'
        );

        // creates index for column `target_id`
        $this->createIndex(
            'idx-post_actions-target_id',
            'post_actions',
            'target_id'
        );

        // add foreign key for table `target`
        $this->addForeignKey(
            'fk-post_actions-target_id',
            'post_actions',
            'target_id',
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
        // drops foreign key for table `initiator`
        $this->dropForeignKey(
            'fk-post_actions-initiator',
            'post_actions'
        );

        // drops index for column `initiator`
        $this->dropIndex(
            'idx-post_actions-initiator',
            'post_actions'
        );

        // drops foreign key for table `target`
        $this->dropForeignKey(
            'fk-post_actions-target_id',
            'post_actions'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            'idx-post_actions-target_id',
            'post_actions'
        );

        $this->dropTable('post_actions');
    }
}
