<?php

use yii\db\Migration;

/**
 * Handles the creation of table `pay_sph_counter`.
 */
class m181218_111328_create_pay_sph_counter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('pay_sph_counter', [
            'id' => $this->primaryKey(),
            'counter_id' => $this->integer(11)->notNull(),
            'max_doc_id' => $this->integer(11)->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('pay_sph_counter');
    }
}
