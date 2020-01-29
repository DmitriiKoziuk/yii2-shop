<?php

namespace DmitriiKoziuk\yii2Shop\migrations;

use yii\db\Migration;

/**
 * Class m200128_124817_add_faceted_nav_index_to_dk_shop_eav_attributes_table
 */
class m200128_124817_add_faceted_nav_index_to_dk_shop_eav_attributes_table extends Migration
{
    private $eavAttributeTableName = '{{%dk_shop_eav_attributes}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'dk_shop_eav_attributes_idx_faceted_nav',
            $this->eavAttributeTableName,
            [
                'selectable',
                'view_at_frontend_faceted_navigation',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'dk_shop_eav_attributes_idx_faceted_nav',
            $this->eavAttributeTableName,
        );
    }
}
