<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\ProductTypeMargin;

class ProductTypeMarginRepository extends AbstractActiveRecordRepository
{
    /**
     * @param int $productTypeId
     * @return ProductTypeMargin[] index by currency_id
     */
    public function getProductTypeMargins(int $productTypeId): array
    {
        $margins = ProductTypeMargin::find()
            ->where([
                'product_type_id' => $productTypeId
            ])
            ->indexBy('currency_id')
            ->all();
        return $margins;
    }
}