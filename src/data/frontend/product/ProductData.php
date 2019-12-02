<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\data\frontend\product;

use DmitriiKoziuk\yii2FileManager\entities\FileEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\entities\Product;

class ProductData
{
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function isMainImageSet(): bool
    {
        return empty($this->product->getMainSku()->getMainImage()) ? false : true;
    }

    public function isTypeSet(): bool
    {
        return $this->product->isTypeSet();
    }

    public function isPriceSet(): bool
    {
        return $this->product->getMainSku()->isCustomerPriceSet();
    }

    public function isCurrencySet(): bool
    {
        return $this->product->getMainSku()->isCurrencySet();
    }

    public function isPreviewAttributesSet(): bool
    {
        return $this->product->getMainSku()->isPreviewAttributeSet();
    }

    public function getId(): int
    {
        return $this->product->getMainSku()->id;
    }

    public function getFullName(): string
    {
        return $this->product->name . ' ' . $this->product->getMainSku()->name;
    }

    public function getUrl(): string
    {
        return $this->product->getMainSku()->url;
    }

    public function getMainImage(): ?FileEntity
    {
        return $this->product->getMainSku()->getMainImage();
    }

    public function getTypeName(): string
    {
        if ($this->isTypeSet()) {
            return $this->product->type->name;
        }
        return '';
    }

    public function getPrice(): int
    {
        return is_null($this->product->getMainSku()->customer_price) ? 0 : $this->product->getMainSku()->customer_price;
    }

    public function getCurrencySymbol(): string
    {
        return  $this->product->getMainSku()->currency->symbol;
    }

    /**
     * @return EavValueVarcharEntity[]|EavValueDoubleEntity[]|EavValueTextEntity[]
     */
    public function getProductPreviewValues(): array
    {
        return $this->product->getMainSku()->getPreviewValues();
    }
}
