<?php

use yii\db\Migration;

/**
 * Handles the creation of table `macrocicle_training_exersise`.
 * Has foreign keys to the tables:
 *
 * - `macrocicle`
 * - `training_exersise`
 */
class m180317_163335_create_junction_table_for_macrocicle_and_training_exersise_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('macrocicle_training_exersise', [
            'macrocicle_id' => $this->integer(),
            'training_exersise_id' => $this->integer(),
            'PRIMARY KEY(macrocicle_id, training_exersise_id)',
        ]);

        // creates index for column `macrocicle_id`
        $this->createIndex(
            'idx-macrocicle_training_exersise-macrocicle_id',
            'macrocicle_training_exersise',
            'macrocicle_id'
        );

        // add foreign key for table `macrocicle`
        $this->addForeignKey(
            'fk-macrocicle_training_exersise-macrocicle_id',
            'macrocicle_training_exersise',
            'macrocicle_id',
            'macrocicle',
            'id',
            'CASCADE'
        );

        // creates index for column `training_exersise_id`
        $this->createIndex(
            'idx-macrocicle_training_exersise-training_exersise_id',
            'macrocicle_training_exersise',
            'training_exersise_id'
        );

        // add foreign key for table `training_exersise`
        $this->addForeignKey(
            'fk-macrocicle_training_exersise-training_exersise_id',
            'macrocicle_training_exersise',
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
        // drops foreign key for table `macrocicle`
        $this->dropForeignKey(
            'fk-macrocicle_training_exersise-macrocicle_id',
            'macrocicle_training_exersise'
        );

        // drops index for column `macrocicle_id`
        $this->dropIndex(
            'idx-macrocicle_training_exersise-macrocicle_id',
            'macrocicle_training_exersise'
        );

        // drops foreign key for table `training_exersise`
        $this->dropForeignKey(
            'fk-macrocicle_training_exersise-training_exersise_id',
            'macrocicle_training_exersise'
        );

        // drops index for column `training_exersise_id`
        $this->dropIndex(
            'idx-macrocicle_training_exersise-training_exersise_id',
            'macrocicle_training_exersise'
        );

        $this->dropTable('macrocicle_training_exersise');
    }
}
