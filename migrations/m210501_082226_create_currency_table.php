<?php

use yii\db\Migration;

class m210501_082226_create_currency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%currency_rate}}', [
            'id' => $this->primaryKey(),
            'currency' => $this->string(8)->notNull(),
            'buy' => $this->decimal(8,2)->notNull(),
            'sell' => $this->decimal(8,2)->notNull(),
            'office_id' => $this->string(16),
            'begins_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx-currency_rate_office_id_begins_at', 'currency_rate', ['office_id', 'begins_at']);
        $this->createIndex('idx-currency_rate_currency', 'currency_rate', 'currency');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%currency_rate}}');
    }
}
