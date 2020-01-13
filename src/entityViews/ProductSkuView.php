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

    public function isOldPriceSet(): bool
    {
        return $this->productSkuEntity->isOldPriceSet();
    }

    public function isCurrencySet(): bool
    {
        return $this->productSkuEntity->isCurrencySet();
    }

    public function isPreviewEavValuesSet(): bool
    {
        return $this->productSkuEntity->isPreviewEavValuesSet();
    }

    public function isInStock(): bool
    {
        return $this->productSkuEntity->isInStock();
    }

    public function isMetaTitleSet(): bool
    {
        return !empty($this->productSkuEntity->meta_title);
    }

    public function isCustomerPriceSet(): bool
    {
        return !empty($this->productSkuEntity->customer_price);
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

    /**
     * @return FileEntity[]
     */
    public function getImages(): array
    {
        $images = $this->productSkuEntity->getImages();
        $firstIDx = array_key_first($images);
        unset($images[$firstIDx]);
        return $images;
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
        if (!is_null($this->productSkuEntity->customer_price)) {
            $price = $this->productSkuEntity->customer_price / 100;
        }
        return (string)$price;
    }

    public function getOldPrice(): string
    {
        $price = '';
        if (!is_null($this->productSkuEntity->old_price)) {
            $price = $this->productSkuEntity->old_price / 100;
            if ($this->productSkuEntity->isCurrencySet()) {
                $price *= $this->productSkuEntity->currency->rate;
            }
        }
        return (string)$price;
    }

    public function getSaving(): float
    {
        return $this->productSkuEntity->getSaving();
    }

    public function getCurrencySymbol(): string
    {
        return $this->productSkuEntity->currency->symbol;
    }

    /**
     * @return EavValueVarcharEntity[]|EavValueDoubleEntity[]|EavValueTextEntity[]
     */
    public function getProductPreviewValues(): array
    {
        return $this->productSkuEntity->getPreviewEavValues();
    }

    /**
     * @return EavValueVarcharEntity[]|EavValueDoubleEntity[]|EavValueTextEntity[]
     */
    public function getEavValues(): array
    {
        return $this->productSkuEntity->getEavValues();
    }

    public function getMetaTitle(): string
    {
        return $this->productSkuEntity->meta_title ?? '';
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

    public function getBreadcrumb(): array
    {
        $breadcrumb = [];
        if ($this->productSkuEntity->product->isCategorySet()) {
            $categoryEntity = $this->productSkuEntity->product->category;
            foreach ($categoryEntity->parents as $parentCategory) {
                $breadcrumb[] = [
                    'label' => $parentCategory->getFrontendName(),
                    'url' => $parentCategory->urlEntity->url,
                ];
            }
            $breadcrumb[] = [
                'label' => $categoryEntity->getFrontendName(),
                'url' => $categoryEntity->urlEntity->url,
            ];
            $breadcrumb[] = [
                'label' => $this->getFullName(),
            ];
        }
        return $breadcrumb;
    }

    public function getDescription(): ?string
    {
        return $this->productSkuEntity->description;
    }
}
