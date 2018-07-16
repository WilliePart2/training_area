<?php

use yii\db\Migration;

/**
 * Handles the creation of table `macrocicle_user_dislike`.
 */
class m180422_125632_create_macrocicle_user_dislike_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('macrocicle_user_dislike', [
            'id' => $this->primaryKey(),
            'comment_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull()
        ]);

        $this->createIndex(
            'idx-macrocicle_user_dislike-comment_id',
            'macrocicle_user_dislike',
            'comment_id'
        );
        $this->addForeignKey(
            'fk-macrocicle_user_dislike-comment_id',
            'macrocicle_user_dislike',
            'comment_id',
            'macrocicle_comments',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-macrocicle_user_dislike-user_id',
            'macrocicle_user_dislike',
            'user_id'
        );
        $this->addForeignKey(
            'fk-macrocicle_user_dislike-user_id',
            'macrocicle_user_dislike',
            'user_id',
            'users',
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
            'idx-macrocicle_user_dislike-comment_id',
            'macrocicle_user_dislike'
        );
        $this->dropForeignKey(
            'fk-macrocicle_user_dislike-comment_id',
            'macrocicle_user_dislike'
        );

        $this->dropIndex(
            'idx-macrocicle_user_dislike-user_id',
            'macrocicle_user_dislike'
        );
        $this->dropForeignKey(
            'fk-macrocicle_user_dislike-user_id',
            'macrocicle_user_dislike'
        );

        $this->dropTable('macrocicle_user_dislike');
    }
}
