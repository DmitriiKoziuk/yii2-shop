<?php declare(strict_types=1);

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

        $query->orderBy(['id' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}