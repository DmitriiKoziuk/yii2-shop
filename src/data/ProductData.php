<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2FileManager\entities\FileEntity;
use DmitriiKoziuk\yii2Shop\entities\Product;

class ProductData
{
    /**
     * @var ProductTypeData
     */
    public $type;

    /**
     * @var ProductSkuData
     */
    public $mainSku;

    /**
     * @var FileEntity[]
     */
    public $images;

    /**
     * @var FileEntity
     */
    public $mainImage;

    /**
     * @var Product
     */
    private $_productRecord;

    public function __construct(Product $productRecord)
    {
        $this->_productRecord = $productRecord;
    }

    public function getId(): int
    {
        return $this->_productRecord->id;
    }

    public function getName(): string
    {
        return $this->_productRecord->name;
    }

    public function getTypeId(): ?int
    {
        return $this->_productRecord->type_id;
    }

    public function getMainSkuId(): int
    {
        return $this->_productRecord->main_sku_id;
    }
}
