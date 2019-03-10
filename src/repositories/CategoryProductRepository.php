<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\CategoryProduct;

class CategoryProductRepository extends AbstractActiveRecordRepository
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