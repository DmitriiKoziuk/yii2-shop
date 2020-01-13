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

    public function isOldPriceSet(): bool
    {
        return $this->productEntity->getMainSku()->isOldPriceSet();
    }

    public function isCurrencySet(): bool
    {
        return $this->productEntity->getMainSku()->isCurrencySet();
    }

    public function isPreviewEavValuesSet(): bool
    {
        return $this->productEntity->getMainSku()->isPreviewEavValuesSet();
    }

    public function isInStock(): bool
    {
        return $this->productEntity->getMainSku()->isInStock();
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

    public function getOldPrice(): string
    {
        $price = '';
        if (! is_null($this->productEntity->getMainSku()->old_price)) {
            $price = $this->productEntity->getMainSku()->old_price / 100;
            if ($this->productEntity->getMainSku()->isCurrencySet()) {
                $price *= $this->productEntity->getMainSku()->currency->rate;
            }
        }
        return (string) $price;
    }

    public function getSaving(): float
    {
        return $this->productEntity->getMainSku()->getSaving();
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
        return $this->productEntity->getMainSku()->getPreviewEavValues();
    }
}
