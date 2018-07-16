<?php

use yii\db\Migration;

/**
 * Class m180605_184419_follow_user_procedure
 */
class m180605_184419_follow_user_procedure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->pdo->exec('DROP FUNCTION IF EXISTS follow_user');
        $this->db->pdo->exec(
            ' CREATE FUNCTION follow_user(follower INT,followed INT) '
            .' RETURNS VARCHAR(20) '
            .' NOT DETERMINISTIC '
            .' BEGIN '
            .' DECLARE result VARCHAR(20); '
            .' DECLARE done INT DEFAULT FALSE;'
            .' DECLARE test_selection INT; '
            .' DECLARE test_selection_cursor CURSOR FOR SELECT id FROM `user_follow` WHERE follower_id=follower AND followed_id=followed; '
            ." DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = TRUE; "
            .' OPEN test_selection_cursor; '
            .' selection_loop: LOOP '
                .' FETCH test_selection_cursor INTO test_selection; '
                .' IF done THEN LEAVE selection_loop; END IF; '
                .' ITERATE selection_loop; '
            .' END LOOP; '
            .' CLOSE test_selection_cursor; '
            .' IF (test_selection) > 0 '
            .' THEN '
                .' DELETE FROM `user_follow` WHERE follower_id=follower AND followed_id=followed; '
                ." SET result = 'unsubscribe';"
            .' ELSE '
                .' INSERT INTO `user_follow` (follower_id, followed_id) VALUES (follower, followed); '
                ." SET result = 'subscribe';"
            .' END IF; '
            .' RETURN result;'
            .' END; '
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $connection = $this->db->pdo;
        $connection->exec('DROP FUNCTION IF EXISTS follow_user');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180605_184419_follow_user_procedure cannot be reverted.\n";

        return false;
    }
    */
}
