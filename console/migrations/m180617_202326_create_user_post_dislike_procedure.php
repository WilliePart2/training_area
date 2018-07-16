<?php

use yii\db\Migration;

/**
 * Class m180617_202326_create_user_post_dislike_procedure
 */
class m180617_202326_create_user_post_dislike_procedure extends Migration
{
    private $tableName = 'post_dislikes';
    private $functionName = 'set_user_post_dislike';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \Yii::$app->db->pdo->exec("DROP FUNCTION IF EXISTS `set_user_post_dislike`");
        \Yii::$app->db->pdo->exec("
            CREATE FUNCTION `set_user_post_dislike`(postId INT, userId BIGINT)
            RETURNS VARCHAR(50)
            BEGIN
                DECLARE result VARCHAR(50);
                IF (SELECT id FROM `post_dislikes` WHERE users_id=userId AND post_id=postId) > 0
                THEN
                    DELETE FROM `post_dislikes` WHERE users_id=userId AND post_id=postId;
                    SET result = 'REMOVE';
                ELSE
                    INSERT INTO `post_dislikes` (users_id, post_id) VALUES (userId, postId);
                    SET result = 'ADD';
                END IF;
                RETURN result;
            END;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \Yii::$app->db->pdo->exec("DROP FUNCTION IF EXISTS {$this->tableName}");
        return true;
    }
}
