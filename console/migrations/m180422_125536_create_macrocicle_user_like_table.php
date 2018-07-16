<?php

use yii\db\Migration;

/**
 * Handles the creation of table `macrocicle_user_like`.
 */
class m180422_125536_create_macrocicle_user_like_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('macrocicle_user_like', [
            'id' => $this->primaryKey(),
            'comment_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull()
        ]);

        $this->createIndex(
            'idx-macrocicle_user_like-comment_id',
            'macrocicle_user_like',
            'comment_id'
        );
        $this->addForeignKey(
            'fk-macrocicle_user_like-comment_id',
            'macrocicle_user_like',
            'comment_id',
            'macrocicle_comments',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-macrocicle_user_like-user_id',
            'macrocicle_user_like',
            'user_id'
        );
        $this->addForeignKey(
            'fk-macrocicle_user_like-user_id',
            'macrocicle_user_like',
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
        $this->dropTable('macrocicle_user_like');
    }
}
