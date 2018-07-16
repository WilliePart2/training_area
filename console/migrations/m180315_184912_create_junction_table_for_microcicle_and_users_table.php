<?php

use yii\db\Migration;

/**
 * Handles the creation of table `microcikle_users`.
 * Has foreign keys to the tables:
 *
 * - `microcikle`
 * - `users`
 */
class m180315_184912_create_junction_table_for_microcicle_and_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('microcicle_users', [
            'id' => $this->primaryKey(),
            'microcicle_id' => $this->integer(),
            'state' => $this->tinyInteger()->notNull()->defaultValue(3),
            'users_id' => $this->integer(),
            'date_complete' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'session_id' => $this->string(255)->notNull()->defaultValue(''),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        // creates index for column `microcikle_id`
        $this->createIndex(
            'idx-microcicle_users-microcicle_id',
            'microcicle_users',
            'microcicle_id'
        );

        // add foreign key for table `microcikle`
        $this->addForeignKey(
            'fk-microcicle_users-microcicle_id',
            'microcicle_users',
            'microcicle_id',
            'microcicle',
            'id',
            'CASCADE'
        );

        // creates index for column `users_id`
        $this->createIndex(
            'idx-microcicle_users-users_id',
            'microcicle_users',
            'users_id'
        );

        // add foreign key for table `users`
        $this->addForeignKey(
            'fk-microcicle_users-users_id',
            'microcicle_users',
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
        // drops foreign key for table `microcikle`
        $this->dropForeignKey(
            'fk-microcicle_users-microcicle_id',
            'microcicle_users'
        );

        // drops index for column `microcikle_id`
        $this->dropIndex(
            'idx-microcicle_users-microcicle_id',
            'microcikle_users'
        );

        // drops foreign key for table `users`
        $this->dropForeignKey(
            'fk-microcicle_users-users_id',
            'microcicle_users'
        );

        // drops index for column `users_id`
        $this->dropIndex(
            'idx-microcicle_users-users_id',
            'microcicle_users'
        );

        $this->dropTable('microcicle_users');
    }
}
