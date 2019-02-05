<?php
namespace DmitriiKoziuk\yii2Shop\services\brand;

use yii\db\Connection;
use DmitriiKoziuk\yii2Base\services\EntityActionService;
use DmitriiKoziuk\yii2Shop\repositories\BrandRepository;
use DmitriiKoziuk\yii2Shop\data\BrandData;

class BrandService extends EntityActionService
{
    private $_brandRepository;

    public function __construct(
        BrandRepository $brandRepository,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_brandRepository = $brandRepository;
    }

    /**
     * @return BrandData[]
     */
    public function getAllBrands(): array
    {
        $brandRecords = $this->_brandRepository->getAllBrands();
        $brands = [];
        foreach ($brandRecords as $brandRecord) {
            $brands[ $brandRecord->id ] = new BrandData($brandRecord);
        }
        return $brands;
    }
}