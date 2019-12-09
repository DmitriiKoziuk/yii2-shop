<?php
namespace DmitriiKoziuk\yii2Shop\services\product;

use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\CategoryProduct;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleProductSkuEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharProductSkuEntity;

class ProductSearchService
{
    public function searchBy(
        ProductSearchParams $params,
        int $productPerPage,
        array $filteredAttributes = null
    ) {
        if (! $params->validate()) {
            throw new \BadMethodCallException('Search params not valid.');
        }

        $query = Product::find();
        $query->innerJoin(
            ProductSku::tableName(),
            [
                ProductSku::tableName() . '.product_id' => new Expression(
                    Product::tableName() . '.id'
                )
            ]
        );
        if (! empty($params->getCategoryId())) {
            $query->innerJoin(
                CategoryProduct::tableName(),
                [
                    CategoryProduct::tableName() . '.product_id'  => new Expression(
                        Product::tableName() . '.id'
                    ),
                ]
            );
            $query->andWhere([
                CategoryProduct::tableName() . '.category_id' => $params->getCategoryId(),
            ]);
        }
        if (! empty($params->stock_status)) {
            $query->andWhere([
                ProductSku::tableName() . '.stock_status' => $params->stock_status
            ]);
        }
        if (! empty($filteredAttributes)) {
            $this->includeEavAttributesToSearchQuery($query, $filteredAttributes);
        }
        $query->distinct();
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $productPerPage,
            ],
        ]);
    }

    /**
     * @param ActiveQuery $query
     * @param EavAttributeEntity[] $filteredAttributes
     * @throws \Exception
     */
    private function includeEavAttributesToSearchQuery(ActiveQuery $query, array $filteredAttributes)
    {
        foreach ($filteredAttributes as $filteredAttribute) {
            switch ($filteredAttribute->storage_type) {
                case EavAttributeEntity::STORAGE_TYPE_VARCHAR:
                    $this->joinVarcharValues($query, $filteredAttribute->values);
                    break;
                case EavAttributeEntity::STORAGE_TYPE_DOUBLE:
                    $this->joinDoubleValues($query, $filteredAttribute->values);
                    break;
                default:
                    throw new \Exception('Storage type logic not implement.');
                    break;
            }
        }
    }

    private function joinVarcharValues(ActiveQuery $query, array $values)
    {
        $query->innerJoin(
            EavValueVarcharProductSkuEntity::tableName(),
            [
                EavValueVarcharProductSkuEntity::tableName() . '.product_sku_id' => new Expression(
                    ProductSku::tableName() . '.id'
                ),
            ]
        );
        $query->andWhere([
            EavValueVarcharProductSkuEntity::tableName() . '.value_id' => ArrayHelper::map($values, 'id', 'id')
        ]);
    }

    private function joinDoubleValues(ActiveQuery $query, array $values)
    {
        $query->innerJoin(
            EavValueDoubleProductSkuEntity::tableName(),
            [
                EavValueDoubleProductSkuEntity::tableName() . '.product_sku_id' => new Expression(
                    ProductSku::tableName() . '.id'
                ),
            ]
        );
        $query->andWhere([
            EavValueDoubleProductSkuEntity::tableName() . '.value_id' => ArrayHelper::map($values, 'id', 'id')
        ]);
    }
}
