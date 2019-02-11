<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\ActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\CategoryProductSku;

final class CategoryProductSkuRepository extends ActiveRecordRepository
{
    /**
     * @param int $productSkuId
     * @return CategoryProductSku[]
     */
    public function getAllProductRelations(int $productSkuId): array
    {
        return CategoryProductSku::find()->where(['product_sku_id' => $productSkuId])->all();
    }
}