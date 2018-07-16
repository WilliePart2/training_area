<?php

use yii\db\Migration;

/**
 * Handles the creation of table `training`.
 */
class m180311_205135_create_training_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('training', [
            'id' => $this->primaryKey(),
            'microcicle_id' => $this->integer()->notNull()->defaultValue(0),
            'name' => $this->string(255)->notNull()->defaultValue(''),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0)
        ]);

        $this->addForeignKey(
            'fk-training-microcicle_id',
            'training',
            'microcicle_id',
            'microcicle',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-training-microcicle_id',
            'training'
        );

        $this->dropTable('training');
    }
}
