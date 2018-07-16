<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users_mentors`.
 * Has foreign keys to the tables:
 *
 * - `users`
 * - `mentors`
 */
class m180311_190551_create_junction_table_for_users_and_mentors_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users_mentors', [
            'users_id' => $this->integer(),
            'mentors_id' => $this->integer(),
            'status' => $this->tinyInteger()->notNull(),
            'initialithator' => $this->tinyInteger()->notNull(),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'PRIMARY KEY(users_id, mentors_id)',
        ]);

        // creates index for column `users_id`
        $this->createIndex(
            'idx-users_mentors-users_id',
            'users_mentors',
            'users_id'
        );

        // add foreign key for table `users`
        $this->addForeignKey(
            'fk-users_mentors-users_id',
            'users_mentors',
            'users_id',
            'users',
            'id',
            'CASCADE'
        );

        // creates index for column `mentors_id`
        $this->createIndex(
            'idx-users_mentors-mentors_id',
            'users_mentors',
            'mentors_id'
        );

        // add foreign key for table `mentors`
        $this->addForeignKey(
            'fk-users_mentors-mentors_id',
            'users_mentors',
            'mentors_id',
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
        // drops foreign key for table `users`
        $this->dropForeignKey(
            'fk-users_mentors-users_id',
            'users_mentors'
        );

        // drops index for column `users_id`
        $this->dropIndex(
            'idx-users_mentors-users_id',
            'users_mentors'
        );

        // drops foreign key for table `mentors`
        $this->dropForeignKey(
            'fk-users_mentors-mentors_id',
            'users_mentors'
        );

        // drops index for column `mentors_id`
        $this->dropIndex(
            'idx-users_mentors-mentors_id',
            'users_mentors'
        );

        $this->dropTable('users_mentors');
    }
}
