<?php

use yii\grid\GridView;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\data\order\OrderData;
use DmitriiKoziuk\yii2Shop\assets\backend\OrderIndexAsset;

/**
 * @var $this \yii\web\View
 * @var $searchParams \DmitriiKoziuk\yii2Shop\data\order\OrderSearchParams
 * @var $orders \DmitriiKoziuk\yii2Shop\data\order\OrdersData
 */

$this->title = Yii::t(ShopModule::TRANSLATION_ORDER, 'Orders');
$this->params['breadcrumbs'][] = $this->title;

OrderIndexAsset::register($this);
?>

<div class="order-index">
    <?= GridView::widget([
        'dataProvider' => $orders,
        'filterModel' => $searchParams,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'content' => function ($order) {
                    /** @var $order \DmitriiKoziuk\yii2Shop\data\order\OrderData */
                    return $order->getId();
                }
            ],
            [
                'attribute' => 'customerName',
                'content' => function ($order) {
                    /** @var $order \DmitriiKoziuk\yii2Shop\data\order\OrderData */
                    return $order->customer()->getFirstName();
                }
            ],
            [
                'attribute' => 'Total products',
                'content' => function ($order) {
                    /** @var $order OrderData */
                    return $order->cart()->getTotalProduct();
                }
            ],
            [
                'attribute' => 'Price',
                'content' => function ($order) {
                    /** @var $order \DmitriiKoziuk\yii2Shop\data\order\OrderData */
                    return $order->cart()->getTotalPrice();
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]) ?>
</div>
