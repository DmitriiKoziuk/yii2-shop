<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\ActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\Brand;

class BrandRepository extends ActiveRecordRepository
{
    /**
     * @return Brand[]
     */
    public function getAllBrands(): array
    {
        $brands = Brand::find()->all();
        return $brands;
    }
}