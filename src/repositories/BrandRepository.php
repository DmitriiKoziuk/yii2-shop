<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\EntityRepository;
use DmitriiKoziuk\yii2Shop\entities\Brand;

class BrandRepository extends EntityRepository
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