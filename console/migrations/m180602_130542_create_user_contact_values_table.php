<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_contact_values`.
 */
class m180602_130542_create_user_contact_values_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_contact_values', [
            'id' => $this->primaryKey(),
            'users_id' => $this->integer()->notNull(),
            'field_id' => $this->integer()->notNull(),
            'value' => $this->string(255)
        ]);
        /** foreign key for bind to users table */
        $this->createIndex(
            'idx-user_contact_values-users',
            'user_contact_values',
            'users_id'
        );
        $this->addForeignKey(
            'fk-user_contact_values-users_id-users-id',
            'user_contact_values',
            'users_id',
            'users',
            'id',
            'CASCADE'
        );
        /** foreign key for bind to user_contact_fields table */
        $this->createIndex(
            'idx-user_contact_values-field_id-user_contact_fields-id',
            'user_contact_values',
            'field_id'
        );
        $this->addForeignKey(
            'fk-user_contact_values-field_id-user_contact_fields-id',
            'user_contact_values',
            'field_id',
            'user_contact_fields',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-user_contact_values-users',
            'user_contact_values'
        );
        $this->dropForeignKey(
            'fk-user_contact_values-users_id-users-id',
            'user_contact_values'
        );
        $this->dropIndex(
            'idx-user_contact_values-field_id-user_contact_fields-id',
            'user_contact_values'
        );
        $this->dropForeignKey(
            'fk-user_contact_values-field_id-user_contact_fields-id',
            'user_contact_values'
        );
        $this->dropTable('user_contact_values');
    }
}
