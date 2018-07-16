<?php

use yii\db\Migration;

/**
 * Handles the creation of table `exersises`.
 */
class m180311_193706_create_exersises_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('exersises', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->defaultValue(''),
            'group_id' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-exersises-group_id',
            'exersises',
            'group_id',
            'groups_exersises',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-exersises-group_id',
            'exersises'
        );

        $this->dropTable('exersises');
    }
}
