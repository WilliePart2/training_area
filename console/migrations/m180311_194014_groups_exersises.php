<?php

use yii\db\Migration;

/**
 * Class m180311_194014_groups_exersises
 */
class m180311_194014_groups_exersises extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('groups_exersises', [
            'id' => $this->primaryKey(),
            'muskul_group' => $this->string()->notNull()->defaultValue('')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('group_exersises');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180311_194014_groups_exersises cannot be reverted.\n";

        return false;
    }
    */
}
