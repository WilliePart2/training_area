<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_contact_field`.
 */
class m180602_113848_create_user_contact_fields_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_contact_fields', [
            'id' => $this->primaryKey(),
            'label' => $this->string(255)->notNull()->defaultValue(''),
            'icon' => $this->string(255)->notNull()->defaultValue(''),
            // 1 - mentor, 2 - user
            'for' => $this->tinyInteger()->notNull()->defaultValue(0),
            'group' => $this->string(255)->notNull()->defaultValue('')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_contact_field');
    }
}
