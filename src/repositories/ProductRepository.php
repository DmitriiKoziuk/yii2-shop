<?php

namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\data\RepositorySearchMethodResponse;
use DmitriiKoziuk\yii2Shop\entities\Product;

class ProductRepository extends AbstractActiveRecordRepository
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

    /**
     * @param ProductSearchParams $params
     * @param int $limit
     * @param null|int $offset
     * @return RepositorySearchMethodResponse
     */
    public function searchByName(
        ProductSearchParams $params,
        int $limit = 10,
        int $offset = null
    ): RepositorySearchMethodResponse {
        $query = Product::find()
            ->where(['like', 'name', $params->getName()])
            ->limit($limit);
        if (! empty($offset)) {
            $query->offset($offset);
        }
        return new RepositorySearchMethodResponse((int) $query->count(), $query->all());
    }
}