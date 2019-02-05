<?php

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_categories`.
 */
class m190101_153501_create_dk_shop_categories_table extends Migration
{
    private $_categoriesTable = '{{%dk_shop_categories}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_categoriesTable, [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'name' => $this->string(45)->notNull(),
            'name_on_site' => $this->string(45)->defaultValue(NULL),
            'slug' => $this->string(60)->notNull(),
            'url' => $this->string(255)->notNull(),
            'meta_title' => $this->string(255)->defaultValue(NULL),
            'meta_description' => $this->string(500)->defaultValue(NULL),
            'description' => $this->text()->defaultValue(NULL),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx_categories_parent_id',
            $this->_categoriesTable,
            'parent_id'
        );
        $this->createIndex(
            'idx_categories-url',
            $this->_categoriesTable,
            'url',
            true
        );

        $this->addForeignKey(
            'fk_categories_parent_id',
            $this->_categoriesTable,
            'parent_id',
            $this->_categoriesTable,
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_categories_parent_id', $this->_categoriesTable);
        $this->dropTable($this->_categoriesTable);
    }
}
