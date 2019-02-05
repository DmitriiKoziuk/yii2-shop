<?php
namespace DmitriiKoziuk\yii2Shop\services\cart;

use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2FileManager\repositories\FileRepository;
use DmitriiKoziuk\yii2Shop\repositories\CartRepository;
use DmitriiKoziuk\yii2Shop\repositories\CartProductRepository;
use DmitriiKoziuk\yii2Shop\entities\CartProduct;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\data\CartData;

class CartWebService
{
    /**
     * @var CartRepository
     */
    private $_cartRepository;

    /**
     * @var CartProductRepository
     */
    private $_cartProductRepository;

    /**
     * @var FileRepository
     */
    private $_fileRepository;

    public function __construct(
        CartRepository $cartRepository,
        CartProductRepository $cartProductRepository,
        FileRepository $fileRepository
    ) {
        $this->_cartRepository = $cartRepository;
        $this->_cartProductRepository = $cartProductRepository;
        $this->_fileRepository = $fileRepository;
    }

    /**
     * @param string $cartKey
     * @return CartData
     * @throws EntityNotFoundException
     */
    public function getCartByKey(string $cartKey): CartData
    {
        $cart = $this->_cartRepository->getByKey($cartKey);
        if (empty($cart)) {
            throw new EntityNotFoundException("Cart with key '{$cartKey}' not found.");
        }
        /** @var CartProduct[] $cartProducts */
        $cartProducts = $this->_cartProductRepository->getCartProducts($cart->id);
        $productSkusImages = [];
        foreach ($cartProducts as $cartProduct) {
            $productSku = $cartProduct->productSku;
            $productSkusImages[ $productSku->id ] = $this->_fileRepository
                ->getEntityImages(
                    ProductSku::FILE_ENTITY_NAME,
                    $productSku->id
                );;
        }
        return new CartData($cart, $cartProducts, $productSkusImages);
    }

    /**
     * @param int $id
     * @return CartData
     * @throws EntityNotFoundException
     */
    public function getCartById(int $id): CartData
    {
        $cart = $this->_cartRepository->getById($id);
        if (empty($cart)) {
            throw new EntityNotFoundException("Cart with key '{$id}' not found.");
        }
        /** @var CartProduct[] $cartProducts */
        $cartProducts = $this->_cartProductRepository->getCartProducts($cart->id);
        $productSkusImages = [];
        foreach ($cartProducts as $cartProduct) {
            $productSku = $cartProduct->productSku;
            $productSkusImages[ $productSku->id ] = $this->_fileRepository
                ->getEntityImages(
                    ProductSku::FILE_ENTITY_NAME,
                    $productSku->id
                );;
        }
        return new CartData($cart, $cartProducts, $productSkusImages);
    }
}