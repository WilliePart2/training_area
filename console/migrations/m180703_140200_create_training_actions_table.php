<?php

use yii\db\Migration;

/**
 * Handles the creation of table `training_actions`.
 * Has foreign keys to the tables:
 *
 * - `initiator`
 * - `training`
 */
class m180703_140200_create_training_actions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('training_actions', [
            'id' => $this->primaryKey(),
            'initiator' => $this->integer()->notNull(),
            'target_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `initiator`
        $this->createIndex(
            'idx-training_actions-initiator',
            'training_actions',
            'initiator'
        );

        // add foreign key for table `initiator`
        $this->addForeignKey(
            'fk-training_actions-initiator',
            'training_actions',
            'initiator',
            'users',
            'id',
            'CASCADE'
        );

        // creates index for column `target_id`
        $this->createIndex(
            'idx-training_actions-target_id',
            'training_actions',
            'target_id'
        );

        // add foreign key for table `training`
        $this->addForeignKey(
            'fk-training_actions-target_id',
            'training_actions',
            'target_id',
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
        // drops foreign key for table `initiator`
        $this->dropForeignKey(
            'fk-training_actions-initiator',
            'training_actions'
        );

        // drops index for column `initiator`
        $this->dropIndex(
            'idx-training_actions-initiator',
            'training_actions'
        );

        // drops foreign key for table `training`
        $this->dropForeignKey(
            'fk-training_actions-target_id',
            'training_actions'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            'idx-training_actions-target_id',
            'training_actions'
        );

        $this->dropTable('training_actions');
    }
}
