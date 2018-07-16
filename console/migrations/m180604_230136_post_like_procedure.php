<?php

use yii\db\Migration;

/**
 * Class m180604_230136_post_like_procedure
 */
class m180604_230136_post_like_procedure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $connection = \Yii::$app->db;
        $connection->createCommand('DROP PROCEDURE IF EXISTS post_like')->execute();
        $connection->createCommand(
            'CREATE PROCEDURE post_like(IN postId int, IN userId int, OUT operation_type varchar(10))'
            .' BEGIN'
            .' IF (SELECT id FROM `post_likes` WHERE `users_id`=userId AND `post_id`=postId) > 0'
            .' THEN'
                .' DELETE FROM `post_likes` WHERE `users_id`=userId AND `post_id`=postId;'
                ." SET @operation_type = 'delete';"
            .'ELSE'
                .' INSERT INTO `post_likes` (users_id, post_id) VALUES (userId, postId);'
                ." SET @operation_type = 'insert';"
            .'END IF;'
            .'END;'
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \Yii::$app->db->createCommand('DROP PROCEDURE IF EXISTS post_like')->execute();
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180604_230136_post_like_procedure cannot be reverted.\n";

        return false;
    }
    */
}
