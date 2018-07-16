<?php

use yii\db\Migration;

/**
 * Handles the creation of table `training_exersise`.
 */
class m180311_210356_create_training_exersise_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('training_exersise', [
            'id' => $this->primaryKey(),
            'exersise_id' => $this->integer()->notNull()->defaultValue(0),
//            'training_id' => $this->integer()->notNull()->defaultValue(0),
            'one_repeat_maximum' => $this->smallInteger()->notNull()->defaultValue(0),
            'total_repeats_count' => $this->smallInteger()->notNull()->defaultValue(0),
            'total_tonnage' => $this->integer()->notNull()->defaultValue(0),
            'total_average_weight' => $this->smallInteger()->notNull()->defaultValue(0),
            'relative_intensity' => $this->tinyInteger()->notNull()->defaultValue(0)
        ]);

        $this->addForeignKey(
            'fk-training_exersise-exersise_id',
            'training_exersise',
            'exersise_id',
            'exersises',
            'id',
            'CASCADE'
        );

//        $this->addForeignKey(
//            'fk-training_exersise-training_id',
//            'training_exersise',
//            'training_id',
//            'training',
//            'id',
//            'CASCADE'
//        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-training_exersise-exersise_id',
            'training_exersise'
        );
        $this->dropTable('training_exersise');
    }
}
