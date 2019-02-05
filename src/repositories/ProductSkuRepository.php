<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\EntityRepository;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;

final class ProductSkuRepository extends EntityRepository
{
    /**
     * @param $id
     * @return ProductSku|null
     */
    public function getById($id): ?ProductSku
    {
        return ProductSku::findOne($id);
    }
}