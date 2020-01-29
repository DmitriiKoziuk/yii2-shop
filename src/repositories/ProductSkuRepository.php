<?php

namespace DmitriiKoziuk\yii2Shop\repositories;


use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\data\RepositorySearchMethodResponse;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\Brand;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\CategoryProductSku;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleProductSkuEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharProductSkuEntity;
use DmitriiKoziuk\yii2Shop\data\product\ProductSkuSearchParams;
use DmitriiKoziuk\yii2Shop\interfaces\RepositorySearchMethodResponseInterface;

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

    public function search(
        ProductSearchParams $params,
        array $filteredAttributes = null,
        array $filterParams = [],
        array $with = [
            'urlEntity',
            'product',
            'product.type.productPreviewEavAttributes',
            'currency',
            'eavVarcharValues.eavAttribute',
            'eavTextValues.eavAttribute',
            'eavDoubleValues.eavAttribute',
            'mainImageEntity',
        ]
    ): RepositorySearchMethodResponseInterface {
        if (! $params->validate()) {
            throw new \BadMethodCallException('Search params not valid.');
        }

        $query = ProductSku::find();
        if (! empty($with)) {
            $query->with($with);
        }
        $query->innerJoin(
            Product::tableName(),
            [
                ProductSku::tableName() . '.product_id' => new Expression(
                    Product::tableName() . '.id'
                ),
            ]
        );
        if (! empty($params->getCategoryIDs())) {
            $query->addSelect([
                ProductSku::tableName() . '.*',
                CategoryProductSku::tableName() . '.sort',
            ]);
            $query->innerJoin(
                CategoryProductSku::tableName(),
                [
                    CategoryProductSku::tableName() . '.product_sku_id'  => new Expression(
                        ProductSku::tableName() . '.id'
                    ),
                ]
            );
            $query->andWhere([
                CategoryProductSku::tableName() . '.category_id' => $params->getCategoryIDs(),
            ]);
            $query->orderBy([
                CategoryProductSku::tableName() . '.sort' => SORT_DESC,
            ]);
        }
        if (! empty($params->stockStatus)) {
            $query->andWhere([
                ProductSku::tableName() . '.stock_status' => $params->getStockStatuses()
            ]);
        }
        if (! empty($filteredAttributes)) {
            $this->includeEavAttributesToSearchQuery($query, $filteredAttributes);
        }
        if (isset($filterParams['brand'])) {
            $this->joinBrand($query, $filterParams);
        }
        $productCount = $query->count();
        $query->limit = $params->getLimit();
        if ($params->isOffsetSet()) {
            $query->offset = $params->getOffset();
        }
        return new RepositorySearchMethodResponse((int) $productCount, $query->all());
    }

    public function getNextSortNumber(int $productID)
    {
        $count = (int) ProductSku::find()->where(['product_id' => $productID])->count();
        return ++$count;
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
