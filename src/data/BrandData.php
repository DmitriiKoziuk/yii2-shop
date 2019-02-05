<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\entities\Brand;

class BrandData
{
    /**
     * @var Brand
     */
    private $_brandRecord;

    public function __construct(Brand $brandRecord)
    {
        $this->_brandRecord = $brandRecord;
    }

    public function getId(): int
    {
        return $this->_brandRecord->id;
    }

    public function getName(): string
    {
        return $this->_brandRecord->name;
    }

    public function getCode(): string
    {
        return $this->_brandRecord->code;
    }

    public function __get($name)
    {
        return $this->{'get' . $name}();
    }
}