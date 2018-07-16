<?php

use yii\db\Migration;

/**
 * Class m180604_230144_post_dislike_procedure
 */
class m180604_230144_post_dislike_procedure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \Yii::$app->db->createCommand('DROP PROCEDURE IF EXISTS post_dislike')->execute();
        \Yii::$app->db->createCommand(
            'CREATE PROCEDURE post_dislike(IN postId int, IN userId int, OUT operation_type varchar(10))'
            .' BEGIN '
            .' IF (SELECT id FROM post_dislikes WHERE `users_id`=userId AND `post_id`=postId) > 0 '
            .' THEN '
                .' DELETE FROM post_dislikes WHERE `users_id`=userId AND `post_id`=postId; '
                ." SET @operation_type='delete'; "
            .' ELSE '
                .' INSERT INTO post_dislikes (users_id, post_id) VALUES (userId, postId); '
                ." SET @operation_type='insert'; "
            .' END IF; '
            .' END; '
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \Yii::$app->db->createCommand('DROP PROCEDURE IF EXISTS post_dislike')->execute();
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180604_230144_post_dislike_procedure cannot be reverted.\n";

        return false;
    }
    */
}
