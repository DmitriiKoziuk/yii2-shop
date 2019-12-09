<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\eventListeners;

use Yii;
use yii\base\Event;
use DmitriiKoziuk\yii2Shop\events\ProductTypeBeforeDeleteEvent;

class ProductEavEventListener
{
    public function __construct()
    {
        $this->productTypeBeforeDeleted();
    }

    private function productTypeBeforeDeleted(): void
    {
        Event::on(
            ProductTypeBeforeDeleteEvent::class,
            ProductTypeBeforeDeleteEvent::EVENT_BEFORE_DELETE,
            function (ProductTypeBeforeDeleteEvent $event) {
                $deleteVarcharRelationSQL = <<<SQL
                DELETE dk_shop_eav_value_varchar_product_sku
                FROM dk_shop_eav_value_varchar_product_sku
                    INNER JOIN dk_shop_product_skus ON dk_shop_product_skus.id = dk_shop_eav_value_varchar_product_sku.product_sku_id
                    INNER JOIN dk_shop_products ON dk_shop_products.id = dk_shop_product_skus.product_id
                WHERE dk_shop_products.type_id = {$event->productTypeId}
                SQL;
                $deleteDoubleRelationSQL = <<<SQL
                DELETE dk_shop_eav_value_double_product_sku
                FROM dk_shop_eav_value_double_product_sku
                    INNER JOIN dk_shop_product_skus ON dk_shop_product_skus.id = dk_shop_eav_value_double_product_sku.product_sku_id
                    INNER JOIN dk_shop_products ON dk_shop_products.id = dk_shop_product_skus.product_id
                WHERE dk_shop_products.type_id = {$event->productTypeId}
                SQL;
                $deleteTextRelationSQL = <<<SQL
                DELETE dk_shop_eav_value_text_product_sku
                FROM dk_shop_eav_value_text_product_sku
                    INNER JOIN dk_shop_product_skus ON dk_shop_product_skus.id = dk_shop_eav_value_text_product_sku.product_sku_id
                    INNER JOIN dk_shop_products ON dk_shop_products.id = dk_shop_product_skus.product_id
                WHERE dk_shop_products.type_id = {$event->productTypeId}
                SQL;
                Yii::$app->db->createCommand($deleteVarcharRelationSQL)->execute();
                Yii::$app->db->createCommand($deleteDoubleRelationSQL)->execute();
                Yii::$app->db->createCommand($deleteTextRelationSQL)->execute();
            }
        );
    }
}
