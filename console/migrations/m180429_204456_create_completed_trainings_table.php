<?php

use yii\db\Migration;

/**
 * Handles the creation of table `completed_trainings`.
 */
class m180429_204456_create_completed_trainings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('completed_trainings', [
            'id' => $this->primaryKey(),
            'training_id' => $this->integer()->notNull(),
            'macrocicle_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'session_id' => $this->string(255)->notNull()
        ]);

        $this->createIndex(
            'idx-completed_trainings-training_id',
            'completed_trainings',
            'training_id'
        );
        $this->addForeignKey(
            'fk-completed_trainings-training_id',
            'completed_trainings',
            'training_id',
            'training',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-completed_trainings-macrocicle_id',
            'completed_trainings',
            'macrocicle_id'
        );
        $this->addForeignKey(
            'fk-completed_trainings-mcarocicle_id',
            'completed_trainings',
            'macrocicle_id',
            'macrocicle',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-completed_trainings-user_id',
            'completed_trainings',
            'user_id'
        );
        $this->addForeignKey(
            'fk-completed_trainings-user_id',
            'completed_trainings',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-completed_trainings-session_id',
            'completed_trainings',
            'session_id'
        );
        $this->addForeignKey(
            'fk-completed_trainings-session_id',
            'completed_trainings',
            'session_id',
            'macrocicle_users',
            'session_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('completed_trainings');
    }
}
