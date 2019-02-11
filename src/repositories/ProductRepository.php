<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\ActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\Product;

final class ProductRepository extends ActiveRecordRepository
{
    public function getById(int $id): ?Product
    {
        /** @var Product|null $product */
        $product = Product::find()->where(['id' => $id])->one();
        return $product;
    }
}