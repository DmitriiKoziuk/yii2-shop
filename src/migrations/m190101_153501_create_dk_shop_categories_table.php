<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `dk_shop_categories`.
 */
class m190101_153501_create_dk_shop_categories_table extends Migration
{
    private $categoriesTable = '{{%dk_shop_categories}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->categoriesTable, [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'name' => $this->string(45)->notNull(),
            'name_on_site' => $this->string(45)->defaultValue(NULL),
            'slug' => $this->string(60)->notNull(),
            'meta_title' => $this->string(255)->defaultValue(NULL),
            'meta_description' => $this->string(500)->defaultValue(NULL),
            'description' => $this->text()->defaultValue(NULL),
            'is_products_show' => $this->boolean()->notNull()->defaultValue(1),
            'template_name' => $this->string(100)->null()->defaultValue(NULL),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex(
            'idx_categories_parent_id',
            $this->categoriesTable,
            'parent_id'
        );
        $this->createIndex(
            'dk_shop_categories_uidx_slug',
            $this->categoriesTable,
            [
                'parent_id',
                'slug',
            ]
        );

        $this->addForeignKey(
            'fk_categories_parent_id',
            $this->categoriesTable,
            'parent_id',
            $this->categoriesTable,
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
        $this->dropForeignKey('fk_categories_parent_id', $this->categoriesTable);
        $this->dropTable($this->categoriesTable);
    }
}
