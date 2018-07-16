<?php

use yii\db\Migration;

/**
 * Handles the creation of table `completed_microcicles`.
 */
class m180430_103159_create_completed_microcicles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('completed_microcicles', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'macrocicle_id' => $this->integer()->notNull(),
            'microcicle_id' => $this->integer()->notNull(),
            'session_id' => $this->string(255)->notNull()
        ]);

        $this->createIndex(
            'idx-completed_microcicles-user_id',
            'completed_microcicles',
            'user_id'
        );
        $this->addForeignKey(
            'fk-completed_microcicles-user_id',
            'completed_microcicles',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-completed_microcicles-macrocicle_id',
            'completed_microcicles',
            'macrocicle_id'
        );
        $this->addForeignKey(
            'fk-completed_microcicles-macrocicle_id',
            'completed_microcicles',
            'macrocicle_id',
            'macrocicle',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-completed_microcicles-microcicle_id',
            'completed_microcicles',
            'microcicle_id'
        );
        $this->addForeignKey(
            'fk-completed_microcicles-microcicle_id',
            'completed_microcicles',
            'microcicle_id',
            'microcicle',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-completed_microcicles-session_id',
            'completed_microcicles',
            'session_id'
        );
        $this->addForeignKey(
            'fk-completed_microcicles-session_id',
            'completed_microcicles',
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
        $this->dropTable('completed_microcicles');
    }
}
