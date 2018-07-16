<?php

use yii\db\Migration;

/**
 * table describe which actions relation to manipulation with user data
 */
/**
 * Handles the creation of table `users_actions`.
 * Has foreign keys to the tables:
 *
 * - `initiator`
 * - `target`
 */
class m180627_174448_create_users_actions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users_actions', [
            'id' => $this->primaryKey(),
            'initiator' => $this->integer()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'action_type' => $this->string(255)->notNull()->defaultValue(''),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // creates index for column `initiator`
        $this->createIndex(
            'idx-users_actions-initiator',
            'users_actions',
            'initiator'
        );

        // add foreign key for table `initiator`
        $this->addForeignKey(
            'fk-users_actions-initiator',
            'users_actions',
            'initiator',
            'users',
            'id',
            'CASCADE'
        );

        // creates index for column `target_id`
        $this->createIndex(
            'idx-users_actions-target_id',
            'users_actions',
            'target_id'
        );

        // add foreign key for table `target`
        $this->addForeignKey(
            'fk-users_actions-target_id',
            'users_actions',
            'target_id',
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
        // drops foreign key for table `initiator`
        $this->dropForeignKey(
            'fk-users_actions-initiator',
            'users_actions'
        );

        // drops index for column `initiator`
        $this->dropIndex(
            'idx-users_actions-initiator',
            'users_actions'
        );

        // drops foreign key for table `target`
        $this->dropForeignKey(
            'fk-users_actions-target_id',
            'users_actions'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            'idx-users_actions-target_id',
            'users_actions'
        );

        $this->dropTable('users_actions');
    }
}
