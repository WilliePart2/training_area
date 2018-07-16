<?php

use yii\db\Migration;

/**
 * Handles the creation of table `macrocicle_users`.
 * Has foreign keys to the tables:
 *
 * - `macrocicle`
 * - `users`
 */
class m180315_190048_create_junction_table_for_macrocicle_and_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('macrocicle_users', [
            'id' => $this->primaryKey(),
            'macrocicle_id' => $this->integer()->notNull(),
            'state' => $this->tinyInteger()->notNull()->defaultValue(3),
            'users_id' => $this->integer(),
            'session_id' => $this->string(255)->notNull()->defaultValue(''),
            'date_complete' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        // creates index for column `macrocicle_id`
        $this->createIndex(
            'idx-macrocicle_users-macrocicle_id',
            'macrocicle_users',
            'macrocicle_id'
        );

        // add foreign key for table `macrocicle`
        $this->addForeignKey(
            'fk-macrocicle_users-macrocicle_id',
            'macrocicle_users',
            'macrocicle_id',
            'macrocicle',
            'id',
            'CASCADE'
        );

        // creates index for column `users_id`
        $this->createIndex(
            'idx-macrocicle_users-users_id',
            'macrocicle_users',
            'users_id'
        );

        // add foreign key for table `users`
        $this->addForeignKey(
            'fk-macrocicle_users-users_id',
            'macrocicle_users',
            'users_id',
            'users',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-macrocicle_users-session_id',
            'macrocicle_users',
            'session_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `macrocicle`
        $this->dropForeignKey(
            'fk-macrocicle_users-macrocicle_id',
            'macrocicle_users'
        );

        // drops index for column `macrocicle_id`
        $this->dropIndex(
            'idx-macrocicle_users-macrocicle_id',
            'macrocicle_users'
        );

        // drops foreign key for table `users`
        $this->dropForeignKey(
            'fk-macrocicle_users-users_id',
            'macrocicle_users'
        );

        // drops index for column `users_id`
        $this->dropIndex(
            'idx-macrocicle_users-users_id',
            'macrocicle_users'
        );

        $this->dropTable('macrocicle_users');
    }
}
