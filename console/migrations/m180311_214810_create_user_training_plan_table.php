<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_training_plan`.
 */
class m180311_214810_create_user_training_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_training_plan', [
            'id' => $this->primaryKey(),
            'weight' => $this->smallInteger()->notNull()->defaultValue(0),
            'repeats' => $this->tinyInteger()->notNull()->defaultValue(0),
            'repeat_section' => $this->tinyInteger()->notNull()->defaultValue(0),
            'repeats_count' => $this->smallInteger()->notNull()->defaultValue(0),
            'tonnage' => $this->integer()->notNull()->defaultValue(0),
            'average_weight' => $this->tinyInteger()->notNull()->defaultValue(0),
            'relative_intensity' => $this->tinyInteger()->notNull()->defaultValue(0),
            'base_training_plan_id' => $this->integer()->notNull()->defaultValue(0),
            'user_id' => $this->integer()->notNull(),
            'session_id' => $this->string(255)->notNull(),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0)
        ]);

        $this->createIndex(
            'idx-user_training_plan-base_training_plan',
            'user_training_plan',
            'base_training_plan_id'
        );
        $this->addForeignKey(
            'fk-user_training_plan-base_training_plan_id',
            'user_training_plan',
            'base_training_plan_id',
            'base_training_plan',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-user_training_plan-user_id',
            'user_training_plan',
            'user_id'
        );
        $this->addForeignKey(
            'fk-user_training_plan-user_id',
            'user_training_plan',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-user_training_plan-session_id',
            'user_training_plan',
            'session_id'
        );
        $this->addForeignKey(
            'fk-user_training_plan-session_id',
            'user_training_plan',
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
        $this->dropTable('user_training_plan');
    }
}
