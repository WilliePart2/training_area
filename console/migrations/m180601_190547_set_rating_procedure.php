<?php

use yii\db\Migration;

/**
 * Class m180601_190547_set_rating_procedure
 */
class m180601_190547_set_rating_procedure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $connection = \Yii::$app->db;
        $connection->createCommand('DROP PROCEDURE IF EXISTS setRating')->execute();
        $connection->createCommand('
            CREATE PROCEDURE setRating (IN userID int, IN macrocicleID int, IN ratingVal int)
            BEGIN
                IF (SELECT id FROM `macrocicle_rating` WHERE users_id=userID AND macrocicle_id=macrocicleID) >= 1
                THEN UPDATE `macrocicle_rating` SET rating=ratingVal WHERE users_id=userID AND macrocicle_id=macrocicleID;
                ELSE INSERT INTO `macrocicle_rating` (users_id, macrocicle_id, rating) VALUES (userID, macrocicleID, ratingVal);
                END IF;
            END;
        ')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180601_190547_set_rating_procedure cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180601_190547_set_rating_procedure cannot be reverted.\n";

        return false;
    }
    */
}
