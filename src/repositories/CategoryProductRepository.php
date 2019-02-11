<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\ActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\CategoryProduct;

final class CategoryProductRepository extends ActiveRecordRepository
{
    /**
     * @param int $productId
     * @return CategoryProduct[]
     */
    public function getAllProductRelations(int $productId): array
    {
        return CategoryProduct::find()->where(['product_id' => $productId])->all();
    }
}