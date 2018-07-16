<?php

use yii\db\Migration;

/**
 * Handles the creation of table `baseTrainingPlan`.
 */
class m180311_213133_create_base_training_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('base_training_plan', [
            'id' => $this->primaryKey(),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0),
            'training_exersise_id' => $this->integer()->notNull()->defaultValue(0),
            'weight' => $this->smallInteger()->notNull()->defaultValue(0),
            'repeats' => $this->tinyInteger()->notNull()->defaultValue(0),
            'repeat_section' => $this->tinyInteger()->notNull()->defaultValue(0),
            'repeat_count' => $this->smallInteger()->notNull()->defaultValue(0),
            'tonnage' => $this->smallInteger()->notNull()->defaultValue(0),
            'average_weight' => $this->tinyInteger()->notNull()->defaultValue(0),
            'relative_intensity' => $this->tinyInteger()->notNull()->defaultValue(0)
        ]);

        $this->addForeignKey(
            'fk-base_training_plan-training_exersise_id',
            'base_training_plan',
            'training_exersise_id',
            'training_exersise',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('baseTrainingPlan');
    }
}
