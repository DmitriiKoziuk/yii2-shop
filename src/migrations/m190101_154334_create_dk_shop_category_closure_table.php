<?php
namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_category_closure`.
 */
class m190101_154334_create_dk_shop_category_closure_table extends Migration
{
    private $_categoryClosureTable = '{{%dk_shop_category_closure}}';
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
        $this->createTable($this->_categoryClosureTable, [
            'id' => $this->primaryKey(),
            'ancestor' => $this->integer()->notNull(),
            'descendant' => $this->integer()->notNull(),
            'depth' => $this->integer()->notNull()->unsigned(),
        ], $tableOptions);
        $this->createIndex(
            'idx_category_closure_ancestor',
            $this->_categoryClosureTable,
            'ancestor'
        );
        $this->createIndex(
            'idx_category_closure_descendant',
            $this->_categoryClosureTable,
            'descendant'
        );

        $this->addForeignKey(
            'fk_category_closure_ancestor',
            $this->_categoryClosureTable,
            'ancestor',
            $this->_categoriesTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_category_closure-descendant',
            $this->_categoryClosureTable,
            'descendant',
            $this->_categoriesTable,
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_category_closure-descendant',
            $this->_categoryClosureTable
        );
        $this->dropForeignKey(
            'fk_category_closure_ancestor',
            $this->_categoryClosureTable
        );
        $this->dropTable($this->_categoryClosureTable);
    }
}
