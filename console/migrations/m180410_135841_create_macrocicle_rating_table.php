<?php

use yii\db\Migration;

/**
 * Handles the creation of table `macrocicle_rating`.
 */
class m180410_135841_create_macrocicle_rating_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('macrocicle_rating', [
            'id' => $this->primaryKey(),
            'users_id' => $this->integer()->notNull()->defaultValue(0),
            'macrocicle_id' => $this->integer()->notNull()->defaultValue(0),
            'rating' => $this->tinyInteger()->notNull()->defaultValue(0)
        ]);

        $this->createIndex(
            'idx-macrocicle_rating-users_id',
            'macrocicle_rating',
            'users_id'
        );
        $this->addForeignKey(
            'fk-macrocicle_rating-users_id',
            'macrocicle_rating',
            'users_id',
            'users',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-macrocicle_rating-macrocicle_id',
            'macrocicle_rating',
            'macrocicle_id'
        );
        $this->addForeignKey(
            'fk-macrocicle_rating-macrocicle_id',
            'macrocicle_rating',
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
        $this->dropTable('macrocicle_rating');
    }
}
