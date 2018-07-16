<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m180311_135622_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $db = \Yii::$app->db;
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->defaultValue(''),
            'password' => $this->string(255)->notNull()->defaultValue(''),
            'email' => $this->string(255)->notNull()->defaultValue(''),
            'registrationDate' => $this->datetime()->notNull()->defaultExpression($db->createCommand('CURRENT_TIMESTAMP')->getRawSql()),
            'type' => $this->string(10)->notNull()->defaultValue('user')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
    }
}
