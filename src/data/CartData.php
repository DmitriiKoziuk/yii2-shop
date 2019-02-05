<?php
namespace DmitriiKoziuk\yii2Shop\data;

use DmitriiKoziuk\yii2FileManager\entities\File;
use DmitriiKoziuk\yii2Shop\entities\Cart;
use DmitriiKoziuk\yii2Shop\entities\CartProduct;

class CartData
{
    /**
     * @var Cart
     */
    private $_cart;

    /**
     * @var CartProductData[]
     */
    private $_cartProducts;

    public function __construct(Cart $cart, array $cartProducts, array $productSkusImages)
    {
        $this->_cart = $cart;
        $this->_setCartProducts($cartProducts, $productSkusImages);
    }

    /**
     * @param CartProduct[] $cartProducts
     * @param File[] $productSkusImages
     */
    private function _setCartProducts(array $cartProducts, array $productSkusImages)
    {
        foreach ($cartProducts as $cartProduct) {
            $productSkuImages = $productSkusImages[ $cartProduct->productSku->id ] ?? [];
            $this->_cartProducts[] = new CartProductData($cartProduct, $productSkuImages);
        }
    }

    public function getCustomerId(): int
    {
        return $this->_cart->customer_id;
    }

    /**
     * @return CartProductData[]
     */
    public function getProducts(): array
    {
        return $this->_cartProducts;
    }

    public function getTotalPrice(): int
    {
        $totalPrice = 0.00;
        foreach ($this->_cartProducts as $cartProduct) {
            if ($cartProduct->getFinalPrice() != 0) {
                $totalPrice += $cartProduct->getFinalPrice();
            }
        }
        return ceil($totalPrice);
    }

    public function getTotalProduct(): int
    {
        $totalProduct = 0;
        foreach ($this->_cartProducts as $product) {
            $totalProduct += $product->getQuantity();
        }
        return $totalProduct;
    }
}