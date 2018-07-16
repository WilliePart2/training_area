<?php

use yii\db\Migration;

/**
 * Handles the creation of table `training_exersise_training`.
 * Has foreign keys to the tables:
 *
 * - `training_exersise`
 * - `training`
 */
class m180317_173925_create_junction_table_for_training_exersise_and_training_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('training_exersise_training', [
            'training_exersise_id' => $this->integer(),
            'training_id' => $this->integer(),
            'PRIMARY KEY(training_exersise_id, training_id)',
        ]);

        // creates index for column `training_exersise_id`
        $this->createIndex(
            'idx-training_exersise_training-training_exersise_id',
            'training_exersise_training',
            'training_exersise_id'
        );

        // add foreign key for table `training_exersise`
        $this->addForeignKey(
            'fk-training_exersise_training-training_exersise_id',
            'training_exersise_training',
            'training_exersise_id',
            'training_exersise',
            'id',
            'CASCADE'
        );

        // creates index for column `training_id`
        $this->createIndex(
            'idx-training_exersise_training-training_id',
            'training_exersise_training',
            'training_id'
        );

        // add foreign key for table `training`
        $this->addForeignKey(
            'fk-training_exersise_training-training_id',
            'training_exersise_training',
            'training_id',
            'training',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `training_exersise`
        $this->dropForeignKey(
            'fk-training_exersise_training-training_exersise_id',
            'training_exersise_training'
        );

        // drops index for column `training_exersise_id`
        $this->dropIndex(
            'idx-training_exersise_training-training_exersise_id',
            'training_exersise_training'
        );

        // drops foreign key for table `training`
        $this->dropForeignKey(
            'fk-training_exersise_training-training_id',
            'training_exersise_training'
        );

        // drops index for column `training_id`
        $this->dropIndex(
            'idx-training_exersise_training-training_id',
            'training_exersise_training'
        );

        $this->dropTable('training_exersise_training');
    }
}
