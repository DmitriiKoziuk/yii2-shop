<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use yii\db\Expression;
use DmitriiKoziuk\yii2Base\repositories\AbstractActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\Cart;

class CartRepository extends AbstractActiveRecordRepository
{
    public function getByKey(string $cartKey): ?Cart
    {
        /** @var Cart|null $cart */
        $cart = Cart::find()
            ->where(
                ['key' => new Expression(':key')],
                [':key' => $cartKey]
            )
            ->one();
        return $cart;
    }

    public function getById(int $id): ?Cart
    {
        /** @var Cart|null $cart */
        $cart = Cart::find()
            ->where(
                ['id' => new Expression(':id')],
                [':id' => $id]
            )
            ->one();
        return $cart;
    }
}