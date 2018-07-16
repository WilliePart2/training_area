<?php

use yii\db\Migration;

/**
 * Class m180618_212107_create_increment_user_post_view_counter_procedure
 */
class m180618_212107_create_increment_user_post_view_counter_procedure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \Yii::$app->db->pdo->exec('DROP PROCEDURE IF EXISTS `increment_user_post_view_counter`');
        \Yii::$app->db->pdo->exec('
            CREATE PROCEDURE increment_user_post_view_counter(IN userId INT, IN postId BIGINT)
            BEGIN
                IF (SELECT id FROM `user_post_views` WHERE users_id=userId AND post_id=postId) > 0
                THEN
                    UPDATE `posts` SET views=views+1 WHERE id=postId;
                ELSE
                    INSERT INTO `user_post_views` (users_id, post_id) VALUES (userId, postId);
                    UPDATE `posts` SET views=views+1 WHERE id=postId;
                END IF;
            END;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \Yii::$app->db->pdo->exec('DROP PROCEDURE IF EXISTS `increment_user_post_view_counter`');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180618_212107_create_increment_user_post_view_counter_procedure cannot be reverted.\n";

        return false;
    }
    */
}
