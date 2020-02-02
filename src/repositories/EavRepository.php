<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\repositories;

use Exception;
use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\entities\CategoryProductSku;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextProductSkuEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleProductSkuEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharProductSkuEntity;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\Brand;

class EavRepository
{
    /**
     * @param int $categoryId
     * @param EavAttributeEntity[] $filteredAttributes
     * @param array $filterParams
     * @return EavValueDoubleEntity[]
     * @throws Exception
     */
    public function getFacetedDoubleValues(
        int $categoryId,
        array $filteredAttributes,
        array $filterParams
    ): array
    {
        $countedField = EavValueDoubleProductSkuEntity::tableName() . '.value_id';
        $query = EavValueDoubleEntity::find()
            ->select([
                EavValueDoubleEntity::tableName() . '.id',
                EavValueDoubleEntity::tableName() . '.attribute_id',
                EavValueDoubleEntity::tableName() . '.value',
                EavValueDoubleEntity::tableName() . '.code',
                "COUNT({$countedField}) AS count",
            ])
            ->innerJoin(
                EavValueDoubleProductSkuEntity::tableName(),
                [
                    EavValueDoubleProductSkuEntity::tableName() . '.value_id' => new Expression(
                        EavValueDoubleEntity::tableName() . '.id'
                    )
                ]
            )->innerJoin(
                ProductSku::tableName(),
                [
                    ProductSku::tableName() . '.id' => new Expression(
                        EavValueDoubleProductSkuEntity::tableName() . '.product_sku_id'
                    )
                ]
            )->innerJoin(
                CategoryProductSku::tableName(),
                [
                    CategoryProductSku::tableName() . '.product_sku_id' => new Expression(
                        ProductSku::tableName() . '.id'
                    )
                ]
            )->innerJoin(
                EavAttributeEntity::tableName(),
                [
                    EavAttributeEntity::tableName() . '.id' => new Expression(
                        EavValueDoubleEntity::tableName() . '.attribute_id'
                    )
                ]
            )->where([
                CategoryProductSku::tableName() . '.category_id' => $categoryId,
                EavAttributeEntity::tableName() . '.selectable' => EavAttributeEntity::SELECTABLE_YES,
                EavAttributeEntity::tableName() . '.view_at_frontend_faceted_navigation' =>
                    EavAttributeEntity::VIEW_AT_FRONTEND_FACETED_NAVIGATION_YES,
            ])->groupBy([
                EavValueDoubleProductSkuEntity::tableName() . '.value_id',
            ])->orderBy([
                EavValueDoubleEntity::tableName() . '.value'  => SORT_ASC,
            ])->with([
                'eavAttribute',
                'unit',
            ]);
        if (! empty($filteredAttributes)) {
            $this->includeEavAttributesToSearchQuery($query, $filteredAttributes);
        }
        if (isset($filterParams['brand'])) {
            $this->joinBrand($query, $filterParams);
        }
        return $query->all();
    }

    /**
     * @param int $categoryId
     * @param EavAttributeEntity[] $filteredAttributes
     * @param array $filterParams
     * @return EavValueVarcharEntity[]
     * @throws Exception
     */
    public function getFacetedVarcharValues(
        int $categoryId,
        array $filteredAttributes,
        array $filterParams
    ): array {
        $countedField = EavValueVarcharProductSkuEntity::tableName() . '.value_id';
        $query = EavValueVarcharEntity::find()
            ->select([
                EavValueVarcharEntity::tableName() . '.id',
                EavValueVarcharEntity::tableName() . '.attribute_id',
                EavValueVarcharEntity::tableName() . '.value',
                EavValueVarcharEntity::tableName() . '.code',
                "COUNT({$countedField}) AS count",
            ])->innerJoin(
                EavValueVarcharProductSkuEntity::tableName(),
                [
                    EavValueVarcharProductSkuEntity::tableName() . '.value_id' => new Expression(
                        EavValueVarcharEntity::tableName() . '.id'
                    )
                ]
            )->innerJoin(
                ProductSku::tableName(),
                [
                    ProductSku::tableName() . '.id' => new Expression(
                        EavValueVarcharProductSkuEntity::tableName() . '.product_sku_id'
                    )
                ]
            )->innerJoin(
                CategoryProductSku::tableName(),
                [
                    CategoryProductSku::tableName() . '.product_sku_id' => new Expression(
                        ProductSku::tableName() . '.id'
                    )
                ]
            )->innerJoin(
                EavAttributeEntity::tableName(),
                [
                    EavAttributeEntity::tableName() . '.id' => new Expression(
                        EavValueVarcharEntity::tableName() . '.attribute_id'
                    )
                ]
            )->where([
                CategoryProductSku::tableName() . '.category_id' => $categoryId,
                EavAttributeEntity::tableName() . '.selectable' => EavAttributeEntity::SELECTABLE_YES,
                EavAttributeEntity::tableName() . '.view_at_frontend_faceted_navigation' =>
                    EavAttributeEntity::VIEW_AT_FRONTEND_FACETED_NAVIGATION_YES,
            ])->groupBy([
                EavValueVarcharProductSkuEntity::tableName() . '.value_id',
            ])->orderBy([
                EavValueVarcharEntity::tableName() . '.value' => SORT_ASC,
            ])->with([
                'eavAttribute',
            ]);
        if (! empty($filteredAttributes)) {
            $this->includeEavAttributesToSearchQuery($query, $filteredAttributes);
        }
        if (isset($filterParams['brand'])) {
            $this->joinBrand($query, $filterParams);
        }
        return $query->all();
    }

    /**
     * @param array $attributeCodes
     * @return EavAttributeEntity[]
     */
    public function getFilteredAttributes(array $attributeCodes): array
    {
        return EavAttributeEntity::find()
            ->where(['in', 'code', $attributeCodes])
            ->indexBy('id')
            ->all();
    }

    /**
     * @param array $codes
     * @return EavValueVarcharEntity[]
     */
    public function getVarcharValuesByCodes(array $codes): array
    {
        return EavValueVarcharEntity::find()
            ->where(['in', 'code', $codes])
            ->all();
    }

    /**
     * @param array $codes
     * @return EavValueDoubleEntity[]
     */
    public function getDoubleValuesByCodes(array $codes): array
    {
        return EavValueDoubleEntity::find()
            ->where(['in', 'code', $codes])
            ->all();
    }

    public function removeProductSkuAndValuesRelations(int $productSkuId)
    {
        EavValueVarcharProductSkuEntity::deleteAll(['product_sku_id' => $productSkuId]);
        EavValueDoubleProductSkuEntity::deleteAll(['product_sku_id' => $productSkuId]);
        $textIds = EavValueTextProductSkuEntity::find()
            ->where(['product_sku_id' => $productSkuId])
            ->all();
        $textIds = array_values(ArrayHelper::map($textIds, 'value_id', 'value_id'));
        EavValueTextEntity::deleteAll(['id' => $textIds]);
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
     * @param \DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity[] $values
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
     * @param \DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity[] $values
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
        $query->innerJoin(Product::tableName(), [
            Product::tableName() . '.id' => new Expression(ProductSku::tableName() . '.product_id'),
        ])->innerJoin(Brand::tableName(), [
            Brand::tableName() . '.id' => new Expression(Product::tableName() . '.brand_id'),
        ])->andWhere([
            Brand::tableName() . '.code' => $filterParams['brand'],
        ]);
    }
}
