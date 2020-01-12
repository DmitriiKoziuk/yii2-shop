<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\services\product;

use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\entities\Brand;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\CategoryProduct;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleProductSkuEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharProductSkuEntity;

class ProductSkuSearchService
{
    public function searchBy(
        ProductSearchParams $params,
        int $productPerPage,
        array $filteredAttributes = null,
        array $filterParams = []
    ) {
        if (! $params->validate()) {
            throw new \BadMethodCallException('Search params not valid.');
        }

        $query = ProductSku::find();
        $query->innerJoin(
            Product::tableName(),
            [
                ProductSku::tableName() . '.product_id' => new Expression(
                    Product::tableName() . '.id'
                ),
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
            $query->orderBy([
                CategoryProduct::tableName() . '.sort' => SORT_ASC,
            ]);
            $query->addSelect([
                ProductSku::tableName() . '.*',
                CategoryProduct::tableName() . '.sort',
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
        if (isset($filterParams['brand'])) {
            $this->joinBrand($query, $filterParams);
        }
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
    private function includeEavAttributesToSearchQuery(ActiveQuery $query, array $filteredAttributes): void
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
                    throw new \Exception("Storage type '$filteredAttribute->storage_type' logic not implement.");
                    break;
            }
        }
    }

    /**
     * @param ActiveQuery $query
     * @param EavValueVarcharEntity[] $values
     */
    private function joinVarcharValues(ActiveQuery $query, array $values): void
    {
        $alias = 'alias_vvps_attr_id_' . $values[ array_key_first($values) ]->attribute_id;
        $query->innerJoin(
            [$alias => EavValueVarcharProductSkuEntity::tableName()],
            [
                $alias . '.product_sku_id' => new Expression(
                    ProductSku::tableName() . '.id'
                ),
            ]
        );
        $query->andWhere([
            $alias . '.value_id' => ArrayHelper::map($values, 'id', 'id')
        ]);
    }

    /**
     * @param ActiveQuery $query
     * @param EavValueDoubleEntity[] $values
     */
    private function joinDoubleValues(ActiveQuery $query, array $values): void
    {
        $alias = 'alias_vvps_attr_id_' . $values[ array_key_first($values) ]->attribute_id;
        $query->innerJoin(
            [$alias => EavValueDoubleProductSkuEntity::tableName()],
            [
                $alias . '.product_sku_id' => new Expression(
                    ProductSku::tableName() . '.id'
                ),
            ]
        );
        $query->andWhere([
            $alias . '.value_id' => ArrayHelper::map($values, 'id', 'id')
        ]);
    }

    /**
     * @param ActiveQuery $query
     * @param array $filterParams
     */
    private function joinBrand(ActiveQuery $query, array $filterParams): void
    {
        $query->innerJoin(Brand::tableName(), [
            Brand::tableName() . '.id' => new Expression(Product::tableName() . '.brand_id'),
        ])->andWhere([
            Brand::tableName() . '.code' => $filterParams['brand'],
        ]);
    }
}
