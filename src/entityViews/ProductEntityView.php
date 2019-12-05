<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entityViews;

use DmitriiKoziuk\yii2FileManager\entities\FileEntity;
use DmitriiKoziuk\yii2Shop\entities\Product;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;

class ProductEntityView
{
    private $productEntity;

    public function __construct(Product $productEntity)
    {
        $this->productEntity = $productEntity;
    }

    public function isMainImageSet(): bool
    {
        return empty($this->productEntity->getMainSku()->getMainImage()) ? false : true;
    }

    public function isTypeSet(): bool
    {
        return $this->productEntity->isTypeSet();
    }

    public function isPriceSet(): bool
    {
        return $this->productEntity->getMainSku()->isCustomerPriceSet();
    }

    public function isCurrencySet(): bool
    {
        return $this->productEntity->getMainSku()->isCurrencySet();
    }

    public function isPreviewAttributesSet(): bool
    {
        return $this->productEntity->getMainSku()->isPreviewAttributeSet();
    }

    public function getId(): int
    {
        return $this->productEntity->getMainSku()->id;
    }

    public function getFullName(): string
    {
        return $this->productEntity->name . ' ' . $this->productEntity->getMainSku()->name;
    }

    public function getUrl(): string
    {
        return $this->productEntity->getMainSku()->getUrl();
    }

    public function getMainImage(): ?FileEntity
    {
        return $this->productEntity->getMainSku()->getMainImage();
    }

    public function getTypeName(): string
    {
        if ($this->isTypeSet()) {
            return $this->productEntity->getTypeName();
        }
        return '';
    }

    public function getPrice(): string
    {
        $price = '';
        if (! is_null($this->productEntity->getMainSku()->customer_price)) {
            $price = $this->productEntity->getMainSku()->customer_price / 100;
        }
        return (string) $price;
    }

    public function getCurrencySymbol(): string
    {
        return  $this->productEntity->getMainSku()->currency->symbol;
    }

    /**
     * @return EavValueVarcharEntity[]|EavValueDoubleEntity[]|EavValueTextEntity[]
     */
    public function getProductPreviewValues(): array
    {
        return $this->productEntity->getMainSku()->getPreviewValues();
    }
}
