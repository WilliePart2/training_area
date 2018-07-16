<?php

use yii\db\Migration;

/**
 * Class m180617_200759_create_user_post_like_procedure
 */
class m180617_200759_create_user_post_like_procedure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $procedure_name = 'set_user_post_like';
            $table_name = 'post_likes';
            \Yii::$app->db->pdo->exec("DROP FUNCTION IF EXISTS $procedure_name");
            \Yii::$app->db->pdo->exec("
                CREATE FUNCTION $procedure_name(userId BIGINT, postId INT)
                RETURNS VARCHAR(50)
                NOT DETERMINISTIC
                BEGIN
                    DECLARE result VARCHAR(50);
                    IF (SELECT id FROM $table_name WHERE users_id=userId AND post_id=postId) > 0
                    THEN
                        DELETE FROM $table_name WHERE users_id=userId AND post_id=postId;
                        SET result = 'REMOVE';
                    ELSE
                        INSERT INTO $table_name (users_id, post_id) VALUES (userId, postId);
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
        \Yii::$app->db->pdo->exec('DROP FUNCTION IF EXISTS `set_user_post_like`');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180617_200759_create_user_post_like_procedure cannot be reverted.\n";

        return false;
    }
    */
}
