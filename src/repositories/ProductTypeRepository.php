<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\ProductType;

class ProductTypeRepository extends AbstractActiveRecordRepository
{
    public function getProductTypeById(int $productTypeId): ?ProductType
    {
        /** @var ProductType|null $record */
        $record = ProductType::find()->where(['id' => $productTypeId])->one();
        return $record;
    }
}