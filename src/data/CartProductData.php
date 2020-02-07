<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2Shop\entities\CartProduct;

class CartProductData
{
    /**
     * @var CartProduct
     */
    private $_cartProduct;

    /**
     * @var ProductSkuData
     */
    private $_productSkuData;

    public function __construct(CartProduct $cartProduct, array $productSkuImages)
    {
        $this->_cartProduct = $cartProduct;
        $this->_productSkuData = new ProductSkuData(
            $this->_cartProduct->productSku,
            $productSkuImages
        );
    }

    public function isCurrencySet(): bool
    {
        return $this->_productSkuData->isCurrencySet();
    }

    public function isTypeSet(): bool
    {
        return $this->_productSkuData->isTypeSet();
    }

    public function getQuantity(): int
    {
        return $this->_cartProduct->quantity;
    }

    public function getFinalPrice(): float
    {
        if (empty($this->_cartProduct->productSku->customer_price)) {
            return 0.00;
        } else {
            $price = $this->getQuantity() * $this->_productSkuData->getPriceOnSite();
            return ceil($price);
        }
    }

    public function getSku(): ProductSkuData
    {
        return $this->_productSkuData;
    }

    public function getProductId(): int
    {
        return $this->_productSkuData->getProductId();
    }

    public function getCurrencyRate(): float
    {
        return $this->_productSkuData->getCurrencyRate();
    }

    public function getCurrencySymbol(): string
    {
        return $this->_productSkuData->getCurrencySymbol();
    }

    public function getTypeName(): string
    {
        return $this->_productSkuData->getTypeName();
    }
}
