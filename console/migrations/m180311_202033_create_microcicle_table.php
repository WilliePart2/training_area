<?php

use yii\db\Migration;

/**
 * Handles the creation of table `micricikle`.
 */
class m180311_202033_create_microcicle_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('microcicle', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->defaultValue(''),
            'macrocicle_id' => $this->integer()->notNull()->defaultValue(0),
            'date_begin' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'duration' => $this->integer()->notNull()->defaultValue(0),
            'date_end' => $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0),
            'valid' => $this->tinyInteger()->notNull()->defaultValue(1)
        ]);

        $this->addForeignKey(
            'fk-microcicle_macrocicle_id',
            'microcicle',
            'macrocicle_id',
            'macrocicle',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('micricikle');
    }
}
