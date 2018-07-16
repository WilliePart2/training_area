<?php

use yii\db\Migration;

/**
 * Handles the creation of table `comment`.
 */
class m180315_181922_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('comment', [
            'id' => $this->primaryKey(),
            'base_training_plan_id' => $this->integer()->notNull()->defaultValue(0),
            'text' => $this->text()
        ]);

        $this->addForeignKey(
            'fk-comment-base_training_plan_id-base_training_plan',
            'comment',
            'base_training_plan_id',
            'base_training_plan',
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
            'fk-comment-base_training_plan_id-base_training_plan',
            'comment'
        );
        $this->dropTable('comment');
    }
}
