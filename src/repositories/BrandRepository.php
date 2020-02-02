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
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;

class BrandRepository extends AbstractActiveRecordRepository
{
    public function getByCode(string $code): ?Brand
    {
        /** @var Brand $brand */
        $brand = Brand::find()->where(['code' => $code])->one();
        return $brand;
    }

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
     * @param Brand $filteredBrand
     * @return Brand[]
     * @throws Exception
     */
    public function getBrands(int $categoryId, array $filteredAttributes, Brand $filteredBrand = null): array
    {
        $tableName = Brand::getTableSchema()->fullName;
        $select = <<<SQL
            ANY_VALUE(`{$tableName}`.`id`) AS `id`,
            ANY_VALUE(`{$tableName}`.`name`) AS `name`,
            ANY_VALUE(`{$tableName}`.`code`) AS `code`,
            COUNT(`{$tableName}`.`id`) AS `quantity`,
        SQL;
        $query = Brand::find();
        $query->addSelect($select);
        $query->innerJoin(Product::tableName(), [
                Product::tableName() . '.brand_id' => new Expression(Brand::tableName() . '.id'),
            ])->innerJoin(ProductSku::tableName(), [
                ProductSku::tableName() . '.product_id' => new Expression(Product::tableName() . '.id'),
            ])->innerJoin(CategoryProduct::tableName(), [
                CategoryProduct::tableName() . '.product_id' =>
                    new Expression(Product::tableName() . '.id'),
            ])->where([
                CategoryProduct::tableName() . '.category_id' => $categoryId,
            ]);
        if (! empty($filteredBrand)) {
            $query->andWhere(['!=', Brand::tableName() . '.id', $filteredBrand->id]);
        }
        if (! empty($filteredAttributes)) {
            $this->includeEavAttributesToSearchQuery($query, $filteredAttributes);
        }
        $query->groupBy(Brand::tableName() . '.code');
        $query->orderBy([
            'name' => SORT_ASC,
        ]);
        return $query->all();
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
}
