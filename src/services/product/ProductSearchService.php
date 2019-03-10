<?php
namespace DmitriiKoziuk\yii2Shop\services\product;

use yii\db\Expression;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\CategoryProduct;

class ProductSearchService
{
    public function searchBy(ProductSearchParams $params, int $productPerPage)
    {
        if (! $params->validate()) {
            throw new \BadMethodCallException('Search params not valid.');
        }
        $query = Product::find();
        if (! empty($params->getCategoryId())) {
            $query->innerJoin(
                CategoryProduct::tableName(),
                [
                    CategoryProduct::tableName() . '.product_id'  => new Expression(
                        Product::tableName() . '.id'
                    ),
                    CategoryProduct::tableName() . '.category_id' => $params->getCategoryId(),
                ]
            );
        }
        if (! empty($params->stock_status)) {
            $query->innerJoin(
                ProductSku::tableName(),
                [
                    ProductSku::tableName() . '.product_id' => new Expression(
                        Product::tableName() . '.id'
                    ),
                    ProductSku::tableName() . '.stock_status' => $params->stock_status,
                ]
            );
        }
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $productPerPage,
            ],
        ]);
    }
}