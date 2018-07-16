<?php

use yii\db\Migration;

/**
 * Class m180616_235008_create_set_user_list_vote_procedure
 */
class m180616_235008_create_set_user_list_vote_procedure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $procedure_name = 'set_user_list_vote';
        \Yii::$app->db->pdo->exec("DROP PROCEDURE IF EXISTS $procedure_name");
        $query = "
            CREATE PROCEDURE $procedure_name(IN postId BIGINT, IN userId INT, IN listItemId INT)
            NOT DETERMINISTIC
            SQL SECURITY DEFINER
            BEGIN
                IF 
                    (
                    SELECT list_item_id  
                        FROM `user_post_votes`  
                        WHERE posts_id=postId AND users_id=userId LIMIT 1
                    ) > 0
                THEN 
                    BEGIN
                        DELETE FROM `user_post_votes` WHERE users_id=userId AND posts_id=postId;
                        INSERT INTO `user_post_votes` (users_id, posts_id, list_item_id) VALUES (userId, postId, listItemId);
                    END;
                ELSE
                    INSERT INTO `user_post_votes` (users_id, posts_id, list_item_id) VALUES (userId, postId, listItemId);
                END IF;
            END;
        ";
        \Yii::$app->db->pdo->exec($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \Yii::$app->db->pdo->exec('DROP PROCEDURE IF EXISTS `set_user_list_vote`');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180616_235008_create_set_user_list_vote_procedure cannot be reverted.\n";

        return false;
    }
    */
}
