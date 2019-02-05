<?php
namespace DmitriiKoziuk\yii2Shop\services\order;

use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\data\order\OrderSearchParams;
use DmitriiKoziuk\yii2Shop\entities\Order;

class OrderSearchService
{
    public function searchOrdersBy(OrderSearchParams $orderSearchParams): ActiveDataProvider
    {
        $query = Order::find();

        $query->andFilterWhere(['status' => $orderSearchParams->status]);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}