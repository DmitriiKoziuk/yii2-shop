<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use yii\db\Expression;
use yii\db\ActiveQuery;
use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\data\product\ProductSkuSearchParams;

class ProductSkuRepository extends AbstractActiveRecordRepository
{
    /**
     * @param $id
     * @return ProductSku|null
     */
    public function getById($id): ?ProductSku
    {
        return ProductSku::findOne($id);
    }

    public function searchProductSku(ProductSkuSearchParams $searchParams): ActiveQuery
    {
        if (! $searchParams->validate()) {
            throw new \BadMethodCallException('Search params not valid.');
        }
        $query = ProductSku::find();
        $query->andFilterWhere([
            ProductSku::tableName() . '.id' => $searchParams->product_sku_id,
            ProductSku::tableName() . '.sell_price_strategy' => $searchParams->sell_price_strategy,
            ProductSku::tableName() . '.currency_id' => $searchParams->currency_id,
        ]);
        if (! empty($searchParams->type_id)) {
            $query->innerJoin(
                Product::tableName(),
                [
                    Product::tableName() . '.id' => new Expression(ProductSku::tableName() . '.product_id'),
                    Product::tableName() . '.type_id' => $searchParams->type_id,
                ]
            );
        }
        return $query;
    }
}