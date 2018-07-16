<?php

use yii\db\Migration;

/**
 * Handles the creation of table `macrocicle_commnets`.
 */
class m180410_132336_create_macrocicle_commnets_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('macrocicle_comments', [
            'id' => $this->primaryKey(),
            'users_id' => $this->integer()->notNull()->defaultValue(0),
            'macrocicle_id' => $this->integer()->notNUll()->defaultValue(0),
            'text' => $this->text()->notNull(),
            'date' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'like' => $this->integer()->notNull()->defaultValue(0),
            'dislike' => $this->integer()->notNull()->defaultValue(0)
        ]);

        $this->createIndex(
            'idx-macrocicle_comments-users_id',
            'macrocicle_comments',
            'users_id'
        );
        $this->addForeignKey(
            'fk-macrocicle_comments-users_id',
            'macrocicle_comments',
            'users_id',
            'users',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-macrocicle_comments-macrocicle_id',
            'macrocicle_comments',
            'macrocicle_id'
        );
        $this->addForeignKey(
            'fk-macrocicle_comments-macrocicle_id',
            'macrocicle_comments',
            'macrocicle_id',
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
        $this->dropIndex('idx-macrocicle_comments-users_id', 'macrocicle_comments');
        $this->dropIndex('idx-macrocicle_comments-macrocicle_id', 'macrocicle_comments');
        $this->dropForeignKey('fk-macrocicle_comments-users_id', 'macrocicle_comments');
        $this->dropForeignKey('fk-macrocicle_comments-macrocicle_id', 'macrocicle_comments');
        $this->dropTable('macrocicle_commnets');
    }
}
