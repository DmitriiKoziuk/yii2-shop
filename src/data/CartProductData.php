<?php
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

    public function getQuantity(): int
    {
        return $this->_cartProduct->quantity;
    }

    public function getFinalPrice(): float
    {
        if (empty($this->_cartProduct->productSku->price_on_site)) {
            return 0.00;
        } else {
            $price = $this->getQuantity() * $this->_productSkuData->getPrice();
            return ceil($price);
        }
    }

    public function getSku(): ProductSkuData
    {
        return $this->_productSkuData;
    }
}