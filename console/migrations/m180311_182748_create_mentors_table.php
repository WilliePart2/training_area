<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mentors`.
 */
class m180311_182748_create_mentors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('mentors', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->defaultValue(''),
            'password' => $this->string(255)->notNull()->defaultValue(''),
            'email' => $this->string(255)->notNull()->defaultValue(''),
            'registrationDate' => $this->datetime()->notNull()->defaultExpression($this->db->createCommand('CURRENT_TIMESTAMP')->getRawSql())
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('mentors');
    }
}
