<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2FileManager\entities\FileEntity;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;

class ProductSkuData
{
    /**
     * @var ProductSku
     */
    private $_productSku;

    /**
     * @var FileEntity[]
     */
    private $_productSkuImages;

    public function __construct(ProductSku $productSku, array $productSkuImages = [])
    {
        $this->_productSku = $productSku;
        $this->_productSkuImages = $productSkuImages;
    }

    public function isCurrencySet(): bool
    {
        return $this->_productSku->isCurrencySet();
    }

    public function isTypeSet(): bool
    {
        return $this->_productSku->product->isTypeSet();
    }

    public function getCurrencySymbol(): string
    {
        if ($this->isCurrencySet()) {
            return $this->_productSku->currency->symbol;
        }
        return '';
    }

    public function getCurrencyRate(): float
    {
        if ($this->isCurrencySet()) {
            return (float) $this->_productSku->currency->rate;
        }
        return 1.0;
    }

    public function getId(): int
    {
        return $this->_productSku->id;
    }

    public function getSlug(): string
    {
        return $this->_productSku->slug;
    }

    public function getUrl(): string
    {
        return $this->_productSku->url;
    }

    public function getProductId(): int
    {
        return $this->_productSku->product_id;
    }

    public function getMainImage(): ?FileEntity
    {
        if (empty($this->_productSkuImages)) {
            return null;
        } else {
            /** @var FileEntity[] $allImages */
            $allImages = $this->_productSkuImages;
            $oneImage = array_slice($allImages, 0, 1);
            return array_shift($oneImage);
        }
    }

    public function getFullName(): string
    {
        return $this->_productSku->product->name . ' ' . $this->_productSku->name;
    }

    public function getName(): string
    {
        return $this->_productSku->name;
    }

    public function getTypeName(): string
    {
        return $this->_productSku->product->type->name;
    }

    public function getPriceOnSite(): float
    {
        return (float) is_null($this->_productSku->getCustomerPrice()) ? 0 : $this->_productSku->getCustomerPrice();
    }

    public function isHasImages(): bool
    {
        return empty($this->_productSkuImages) ? false : true;
    }

    public function getMetaTitle(): ?string
    {
        return $this->_productSku->meta_title;
    }

    public function getMetaDescription(): ?string
    {
        return $this->_productSku->meta_description;
    }

    public function getShortDescription(): ?string
    {
        return $this->_productSku->short_description;
    }

    public function getDescription(): ?string
    {
        return $this->_productSku->description;
    }

    public function isCustomerPriceSet(): bool
    {
        return !is_null($this->_productSku->customer_price);
    }
}
