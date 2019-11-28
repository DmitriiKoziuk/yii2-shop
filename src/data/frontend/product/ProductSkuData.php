<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\data\frontend\product;

use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2FileManager\entities\FileEntity;

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
        return $this->productSku->isSitePriceSet();
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

    public function getPrice(): string
    {
        return $this->productSku->price_on_site;
    }
}
