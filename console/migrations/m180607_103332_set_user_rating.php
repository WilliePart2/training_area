<?php

use yii\db\Migration;

/**
 * Class m180607_103332_set_user_rating
 */
class m180607_103332_set_user_rating extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->pdo->exec('DROP PROCEDURE IF EXISTS set_user_rating');
        $this->db->pdo->exec('
            CREATE PROCEDURE set_user_rating(IN voting_user_id INT, IN evaluate_user_id INT, IN rating_value INT)
            BEGIN
                IF (SELECT id FROM `user_rating` WHERE vote_owner_id=voting_user_id AND users_id=evaluate_user_id) > 0
                THEN 
                    UPDATE `user_rating` SET value=rating_value WHERE vote_owner_id=voting_user_id AND users_id=evaluate_user_id;
                ELSE
                    INSERT INTO `user_rating` (vote_owner_id, users_id, value) VALUES (voting_user_id, evaluate_user_id, rating_value);
                END IF;
            END;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->pdo->exec('DROP PROCEDURE IF EXISTS set_user_rating');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180607_103332_set_user_rating cannot be reverted.\n";

        return false;
    }
    */
}
