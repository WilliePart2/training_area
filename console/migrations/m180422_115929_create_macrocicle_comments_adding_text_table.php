<?php

use yii\db\Migration;

class m180422_115929_create_macrocicle_comments_adding_text_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('macrocicle_comments_adding_text', [
            'id' => $this->primaryKey(),
            'comment_id' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

        $this->createIndex(
            'idx-macrocicle_comments_adding_text-comment_id',
            'macrocicle_comments_adding_text',
            'comment_id'
        );

        $this->addForeignKey(
            'fk-macrocicle_comments_adding_text-comment_id',
            'macrocicle_comments_adding_text',
            'comment_id',
            'macrocicle_comments',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-macrocicle_comments_adding_text-comment_id',
            'macrocicle_comments_adding_text'
        );
        $this->dropForeignKey(
            'fk-macrocicle_comments_adding_text-comment_id',
            'macrocicle_comments_adding_text'
        );
        $this->dropTable('macrocicle_comments_adding_text');
    }
}
