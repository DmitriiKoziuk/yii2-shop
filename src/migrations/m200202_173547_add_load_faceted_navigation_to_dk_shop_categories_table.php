<?php

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Class m200202_173547_add_load_faceted_navigation_to_dk_shop_categories_table
 */
class m200202_173547_add_load_faceted_navigation_to_dk_shop_categories_table extends Migration
{
    private $categoriesTable = '{{%dk_shop_categories}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->categoriesTable,
            'load_faceted_navigation',
            $this->boolean()->notNull()->defaultValue(1)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(
            $this->categoriesTable,
            'load_faceted_navigation'
        );
    }
}
