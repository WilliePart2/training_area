<?php

use yii\db\Migration;

/**
 * Handles the creation of table `post_lists`.
 */
class m180604_183357_create_post_lists_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post_lists', [
            'id' => $this->primaryKey(),
            'post_id' => $this->bigInteger()->notNull(),
            'content' => $this->string(255)->notNull()->defaultValue(''),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0)
        ]);

        /** creating index and foreign key for post_id */
        $this->createIndex(
            'idx-post_lists-post_id-posts-id',
            'post_lists',
            'post_id'
        );
        $this->addForeignKey(
            'fk-post_lists-post_id-posts-id',
            'post_lists',
            'post_id',
            'posts',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-post_lists-post_id-posts-id', 'post_lists');
        $this->dropForeignKey('fk-post_lists-post_id-posts-id', 'post_lists');
        $this->dropTable('post_lists');
    }
}
