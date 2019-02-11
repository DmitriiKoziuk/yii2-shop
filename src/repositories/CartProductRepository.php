<?php
namespace DmitriiKoziuk\yii2Shop\repositories;


use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\CartProduct;

class CartProductRepository extends AbstractActiveRecordRepository
{
    public function getRelation(int $cartId, int $productSkuId): ?CartProduct
    {
        /** @var CartProduct|null $relation */
        $relation = CartProduct::find()
            ->where(['cart_id' => $cartId, 'product_sku_id' => $productSkuId])
            ->one();
        return $relation;
    }

    /**
     * @param int $cartId
     * @return CartProduct[]
     */
    public function getCartProducts(int $cartId): array
    {
        return CartProduct::find()
            ->with(['productSku'])
            ->where(['cart_id' => $cartId])
            ->all();
    }
}