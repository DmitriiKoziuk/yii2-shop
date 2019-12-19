<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\repositories;

use Exception;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\Brand;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\CategoryProduct;
use DmitriiKoziuk\yii2Shop\entities\categoryFaceted\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleProductSkuEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharProductSkuEntity;

class BrandRepository extends AbstractActiveRecordRepository
{
    /**
     * @return Brand[]
     */
    public function getAllBrands(): array
    {
        /** @var Brand[] $brands */
        $brands = Brand::find()->all();
        return $brands;
    }

    /**
     * @param int $categoryId
     * @param EavAttributeEntity[] $filteredAttributes
     * @return Brand[]
     * @throws Exception
     */
    public function getFilteredBrands(int $categoryId, array $filteredAttributes): array
    {
        $query = Brand::find()
            ->innerJoin(Product::tableName(), [
                Product::tableName() . '.brand_id' => new Expression(Brand::tableName() . '.id'),
            ])->innerJoin(ProductSku::tableName(), [
                ProductSku::tableName() . '.product_id' => new Expression(Product::tableName() . '.id'),
            ])->innerJoin(CategoryProduct::tableName(), [
                CategoryProduct::tableName() . '.product_id' =>
                    new Expression(Product::tableName() . '.id'),
            ])->where([
                CategoryProduct::tableName() . '.category_id' => $categoryId,
            ]);
        if (! empty($filteredAttributes)) {
            $this->joinFilteredAttributes($query, $filteredAttributes);
        }
        return $query->orderBy([
            Brand::tableName() . '.name' => SORT_ASC,
        ])->all();
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
