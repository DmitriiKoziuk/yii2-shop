<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2FileManager\entities\File;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;

class ProductSkuData
{
    /**
     * @var ProductSku
     */
    private $_productSku;

    /**
     * @var File[]
     */
    private $_productSkuImages;

    public function __construct(ProductSku $productSku, array $productSkuImages = [])
    {
        $this->_productSku = $productSku;
        $this->_productSkuImages = $productSkuImages;
    }

    public function getId(): int
    {
        return (int) $this->_productSku->id;
    }

    public function getMainImage(): ?File
    {
        if (empty($this->_productSkuImages)) {
            return null;
        } else {
            /** @var File[] $allImages */
            $allImages = $this->_productSkuImages;
            $oneImage = array_slice($allImages, 0, 1);
            return array_shift($oneImage);
        }
    }

    public function getFullName(): string
    {
        return $this->_productSku->product->name . ' ' . $this->_productSku->name;
    }

    public function getTypeName(): string
    {
        return $this->_productSku->product->type->name;
    }

    public function getPrice(): int
    {
        $price = $this->_productSku->price_on_site;
        return ceil($price);
    }

    public function isHasImages(): bool
    {
        return empty($this->_productSkuImages) ? false : true;
    }
}