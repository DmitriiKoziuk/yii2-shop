<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\Product;

final class ProductRepository extends AbstractActiveRecordRepository
{
    public function getById(int $id): ?Product
    {
        /** @var Product|null $product */
        $product = Product::find()->where(['id' => $id])->one();
        return $product;
    }

    public function getByName(string $name): ?Product
    {
        /** @var Product|null $productEntity */
        $productEntity = Product::find()->where(['name' => $name])->one();
        return $productEntity;
    }
}