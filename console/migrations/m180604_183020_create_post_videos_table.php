<?php

use yii\db\Migration;

/**
 * Handles the creation of table `post_videos`.
 */
class m180604_183020_create_post_videos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('post_videos', [
            'id' => $this->primaryKey(),
            'post_id' => $this->bigInteger()->notNull(),
            'content' => $this->string(255)->notNull()->defaultValue(''),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0)
        ]);

        /** creating index and foreign key for post_id */
        $this->createIndex(
            'idx-post_videos-post_id-posts-id',
            'post_videos',
            'post_id'
        );
        $this->addForeignKey(
            'fk-post_videos-post_id-posts-id',
            'post_videos',
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
        $this->dropIndex('idx-post_videos-post_id-posts-id', 'post_videos');
        $this->dropForeignKey('fk-post_videos-post_id-posts-id', 'post_videos');
        $this->dropTable('post_videos');
    }
}
