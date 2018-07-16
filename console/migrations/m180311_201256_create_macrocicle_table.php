<?php

use yii\db\Migration;

/**
 * Handles the creation of table `microcicle`.
 */
class m180311_201256_create_macrocicle_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('macrocicle', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->defaultValue(''),
            'mentor_id' => $this->integer()->notNull()->defaultValue(0),
            'visible' => $this->tinyInteger()->notNull()->defaultValue(1),
            'readme' => $this->text(),
            'counter' => $this->integer()->notNull()->defaultValue(0),
            'date' => $this->datetime()->notNull()->defaultExpression(`CURRENT_DATETIME`)
        ]);

        $this->addForeignKey(
            'fk-macrocicle-user_id',
            'macrocicle',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('microcicle');
    }
}
