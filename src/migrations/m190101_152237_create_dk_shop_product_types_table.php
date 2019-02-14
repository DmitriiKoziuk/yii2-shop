<?php
namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_product_types`.
 */
class m190101_152237_create_dk_shop_product_types_table extends Migration
{
    private $_productsTypesTable = '{{%dk_shop_product_types}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->_productsTypesTable, [
            'id'                  => $this->primaryKey(),
            'name'                => $this->string(45)->notNull(),
            'name_on_site'        => $this->string(45)->defaultValue(NULL),
            'code'                => $this->string(55)->notNull(),
            'product_title'       => $this->string(255)->defaultValue(NULL),
            'product_description' => $this->string(350)->defaultValue(NULL),
            'product_url_prefix'  => $this->string(100)->defaultValue(NULL),
            'created_at'          => $this->integer()->unsigned()->notNull(),
            'updated_at'          => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx_product_types_name',
            $this->_productsTypesTable,
            'name',
            true
        );
        $this->createIndex(
            'idx_product_types_code',
            $this->_productsTypesTable,
            'code',
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->_productsTypesTable);
    }
}
