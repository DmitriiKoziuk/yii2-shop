<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\entityViews;

use DmitriiKoziuk\yii2FileManager\entities\FileEntity;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;

class ProductSkuView extends ProductEntityView
{
    /**
     * @var ProductSku
     */
    private $productSkuEntity;

    public function __construct(ProductSku $productSkuEntity)
    {
        parent::__construct($productSkuEntity->product);
        $this->productSkuEntity = $productSkuEntity;
    }

    public function isMainImageSet(): bool
    {
        return empty($this->productSkuEntity->getMainImage()) ? false : true;
    }

    public function isPriceSet(): bool
    {
        return $this->productSkuEntity->isCustomerPriceSet();
    }

    public function isCurrencySet(): bool
    {
        return $this->productSkuEntity->isCurrencySet();
    }

    public function isPreviewAttributesSet(): bool
    {
        return $this->productSkuEntity->isPreviewAttributeSet();
    }

    public function isMetaTitleSet(): bool
    {
        return ! empty($this->productSkuEntity->meta_title);
    }

    public function isCustomerPriceSet(): bool
    {
        return ! empty($this->productSkuEntity->customer_price);
    }

    public function getId(): int
    {
        return $this->productSkuEntity->id;
    }

    public function getFullName(): string
    {
        return $this->productSkuEntity->product->name . ' ' . $this->productSkuEntity->name;
    }

    public function getUrl(): string
    {
        return $this->productSkuEntity->getUrl();
    }

    public function getMainImage(): ?FileEntity
    {
        return $this->productSkuEntity->getMainImage();
    }

    public function getTypeName(): string
    {
        if ($this->isTypeSet()) {
            return $this->productSkuEntity->getTypeName();
        }
        return '';
    }

    public function getPrice(): string
    {
        $price = '';
        if (! is_null($this->productSkuEntity->customer_price)) {
            $price = $this->productSkuEntity->customer_price / 100;
        }
        return (string) $price;
    }

    public function getCurrencySymbol(): string
    {
        return  $this->productSkuEntity->currency->symbol;
    }

    /**
     * @return EavValueVarcharEntity[]|EavValueDoubleEntity[]|EavValueTextEntity[]
     */
    public function getProductPreviewValues(): array
    {
        return $this->productSkuEntity->getPreviewValues();
    }

    public function getMetaTitle(): string
    {
        return $this->productSkuEntity->meta_title;
    }

    public function getProductName(): string
    {
        return $this->productSkuEntity->getProductName();
    }

    public function getName(): string
    {
        return $this->productSkuEntity->getName();
    }

    public function getCustomerPrice()
    {
        return $this->productSkuEntity->customer_price / 100;
    }

    public function getType()
    {
        return $this->productSkuEntity->product->type;
    }
}
