<?php

use yii\db\Migration;

/**
 * Handles the creation of table `macrocicle_actions`.
 * Has foreign keys to the tables:
 *
 * - `initiator`
 * - `target`
 */
class m180627_175148_create_macrocicle_actions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('macrocicle_actions', [
            'id' => $this->primaryKey(),
            'initiator' => $this->integer()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'action_type' => $this->string(255)->notNull()->defaultValue(''),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // creates index for column `initiator`
        $this->createIndex(
            'idx-macrocicle_actions-initiator',
            'macrocicle_actions',
            'initiator'
        );

        // add foreign key for table `initiator`
        $this->addForeignKey(
            'fk-macrocicle_actions-initiator',
            'macrocicle_actions',
            'initiator',
            'users',
            'id',
            'CASCADE'
        );

        // creates index for column `target_id`
        $this->createIndex(
            'idx-macrocicle_actions-target_id',
            'macrocicle_actions',
            'target_id'
        );

        // add foreign key for table `target`
        $this->addForeignKey(
            'fk-macrocicle_actions-target_id',
            'macrocicle_actions',
            'target_id',
            'macrocicle',
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
            'fk-macrocicle_actions-initiator',
            'macrocicle_actions'
        );

        // drops index for column `initiator`
        $this->dropIndex(
            'idx-macrocicle_actions-initiator',
            'macrocicle_actions'
        );

        // drops foreign key for table `target`
        $this->dropForeignKey(
            'fk-macrocicle_actions-target_id',
            'macrocicle_actions'
        );

        // drops index for column `target_id`
        $this->dropIndex(
            'idx-macrocicle_actions-target_id',
            'macrocicle_actions'
        );

        $this->dropTable('macrocicle_actions');
    }
}
