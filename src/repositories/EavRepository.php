<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Shop\entities\EavValueTextProductSkuEntity;
use Yii;
use Exception;
use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\entities\CategoryProductSku;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleProductSkuEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharProductSkuEntity;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;

class EavRepository
{
    /**
     * @param int $categoryId
     * @param EavAttributeEntity[] $filteredAttributes
     * @return EavValueDoubleEntity[]
     * @throws Exception
     */
    public function getFacetedDoubleValues(int $categoryId, array $filteredAttributes): array
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
            ])->groupBy([
                EavValueDoubleProductSkuEntity::tableName() . '.value_id',
            ])->orderBy([
                EavValueDoubleEntity::tableName() . '.value'  => SORT_ASC,
            ])->with([
                'eavAttribute',
                'unit',
            ]);
        if (! empty($filteredAttributes)) {
            $this->joinFilteredAttributes($query, $filteredAttributes);
        }
        return $query->all();
    }

    /**
     * @param int $categoryId
     * @param EavAttributeEntity[] $filteredAttributes
     * @return EavValueVarcharEntity[]
     * @throws Exception
     */
    public function getFacetedVarcharValues(int $categoryId, array $filteredAttributes): array
    {
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
            ])->groupBy([
                EavValueVarcharProductSkuEntity::tableName() . '.value_id',
            ])->orderBy([
                EavValueVarcharEntity::tableName() . '.value' => SORT_ASC,
            ])->with([
                'eavAttribute',
            ]);
        if (! empty($filteredAttributes)) {
            $this->joinFilteredAttributes($query, $filteredAttributes);
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
     * @throws Exception
     */
    private function joinFilteredAttributes(ActiveQuery $query, array $filteredAttributes)
    {
        $varcharValueIds = [];
        $doubleValueIds = [];
        foreach ($filteredAttributes as $attribute) {
            switch ($attribute->storage_type) {
                case EavAttributeEntity::STORAGE_TYPE_VARCHAR:
                    $varcharValueIds = array_merge($varcharValueIds, ArrayHelper::map($attribute->values, 'id','id'));
                    break;
                case EavAttributeEntity::STORAGE_TYPE_DOUBLE:
                    $doubleValueIds = array_merge($doubleValueIds, ArrayHelper::map($attribute->values, 'id','id'));
                    break;
                default:
                    throw new Exception('Not supported storage type');
                    break;
            }
        }
        if (! empty($varcharValueIds)) {
            $tableName = EavValueVarcharProductSkuEntity::tableName();
            $query->innerJoin(
                ["{$tableName} AS filtered_varchar"],
                [
                    'filtered_varchar.product_sku_id' => new Expression(ProductSku::tableName() . '.id'),
                ]
            );
            $query->andWhere([
                'filtered_varchar.value_id' => $varcharValueIds,
            ]);
        }
        if (! empty($doubleValueIds)) {
            $tableName = EavValueDoubleProductSkuEntity::tableName();
            $query->innerJoin(
                ["{$tableName} AS filtered_double"],
                [
                    'filtered_double.product_sku_id' => new Expression(ProductSku::tableName() . '.id'),
                ]
            );
            $query->andWhere([
                'filtered_double.value_id' => $doubleValueIds,
            ]);
        }
    }
}
