<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m251106_115441_create_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey(),
            'author' => $this->string(15)->notNull(),
            'email' => $this->string()->notNull(),
            'message' => $this->text()->notNull(),
            'ip' => $this->string(45)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'deleted_at' => $this->integer()->null(),
            'edit_token' => $this->string(64)->null(),
            'delete_token' => $this->string(64)->null(),
        ]);

        $this->createIndex('idx-post-ip', '{{%post}}', 'ip');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%post}}');
    }
}
