<?php

use yii\db\Migration;

/**
 * Handles the creation of table `post_articles`.
 */
class m180604_182334_create_post_articles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post_articles', [
            'id' => $this->primaryKey(),
            'post_id' => $this->bigInteger()->notNull(),
            'content' => $this->text()->notNull(),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0)
        ]);

        /** creating index and foreign key for post_id */
        $this->createIndex(
            'idx-post_articles-post_id-posts-id',
            'post_articles',
            'post_id'
        );
        $this->addForeignKey(
            'fk-post_articles-post_id-posts-id',
            'post_articles',
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
        $this->dropIndex('idx-post_articles-post_id-posts-id', 'post_articles');
        $this->dropForeignKey('fk-post_articles-post_id-posts-id', 'post_articles');
        $this->dropTable('post_articles');
    }
}
