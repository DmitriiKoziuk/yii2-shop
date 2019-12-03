<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\data\frontend\product;

use DmitriiKoziuk\yii2FileManager\entities\FileEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;

class ProductSkuData extends ProductData
{
    /**
     * @var ProductSku
     */
    private $productSku;

    public function __construct(ProductSku $productSku)
    {
        parent::__construct($productSku->product);
        $this->productSku = $productSku;
    }

    public function isMainImageSet(): bool
    {
        return empty($this->productSku->getMainImage()) ? false : true;
    }

    public function isPriceSet(): bool
    {
        return $this->productSku->isCustomerPriceSet();
    }

    public function isCurrencySet(): bool
    {
        return $this->productSku->isCurrencySet();
    }

    public function isPreviewAttributesSet(): bool
    {
        return $this->productSku->isPreviewAttributeSet();
    }

    public function getId(): int
    {
        return $this->productSku->id;
    }

    public function getFullName(): string
    {
        return $this->product->name . ' ' . $this->productSku->name;
    }

    public function getUrl(): string
    {
        return $this->productSku->url;
    }

    public function getMainImage(): ?FileEntity
    {
        return $this->productSku->getMainImage();
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
        return is_null($this->productSku->customer_price) ? 0 : $this->productSku->customer_price;
    }

    public function getCurrencySymbol(): string
    {
        return  $this->productSku->currency->symbol;
    }

    /**
     * @return EavValueVarcharEntity[]|EavValueDoubleEntity[]|EavValueTextEntity[]
     */
    public function getProductPreviewValues(): array
    {
        return $this->product->getMainSku()->getPreviewValues();
    }
}
