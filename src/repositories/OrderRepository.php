<?php
namespace DmitriiKoziuk\yii2Shop\repositories;

use DmitriiKoziuk\yii2Base\repositories\ActiveRecordRepository;
use DmitriiKoziuk\yii2Shop\entities\Order;

class OrderRepository extends ActiveRecordRepository
{
    public function getOrderById(int $id): ?Order
    {
        /** @var Order $order */
        $order = Order::find()->where(['id' => $id])->one();
        return $order;
    }
}