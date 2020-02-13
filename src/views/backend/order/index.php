<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2UserManager\entities\User;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\entities\Order;
use DmitriiKoziuk\yii2Shop\entities\Currency;
use DmitriiKoziuk\yii2Shop\entities\OrderStageLog;
use DmitriiKoziuk\yii2Shop\entities\search\OrderSearch;
use DmitriiKoziuk\yii2Shop\assets\backend\OrderIndexAsset;

/**
 * @var $this         View
 * @var $searchModel  OrderSearch
 * @var $dataProvider ActiveDataProvider
 * @var $currencies   Currency[]
 * @var $mainCurrency Currency
 * @var $users        User[]
 */

$this->title = Yii::t(ShopModule::TRANSLATION_ORDER, 'Orders');
$this->params['breadcrumbs'][] = $this->title;
OrderIndexAsset::register($this);
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($order, $key, $index, $grid) {
            /** @var Order $order */
            $class = $order->currentStage->getStatusCode();

            return [
                'class'=>$class
            ];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Product types in order'),
                'content' => function ($order) {
                    /** @var Order $order */
                    return join(',', $order->cart->getProductTypes());
                },
            ],
            [
                'label' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Order status'),
                'content' => function ($order) {
                    /** @var Order $order */
                    return Html::tag(
                            'div',
                            Yii::t(ShopModule::TRANSLATION_ORDER_STAGES, $order->currentStage->getStatusName()),
                            ['class' => 'status-name ' . $order->currentStage->getStatusCode()]
                    );
                },
            ],
            [
                'label' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Manager'),
                'attribute' => 'user_id',
                'content' => function ($order) use ($users) {
                    /** @var Order $order */
                    if (
                      ! empty($order->currentStage->user_id)
                    ) {
                      return $users[ $order->currentStage->user_id ]->username;
                    }
                    return '';
                },
                'filter' => ArrayHelper::map($users, 'id', 'username'),
            ],
            [
                'label' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Total product ordered'),
                'content' => function ($order) {
                    /** @var Order $order */
                    return $order->cart->getTotalProducts();
                },
            ],
            [
                'label' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Total order price'),
                'content' => function ($order) use ($currencies, $mainCurrency) {
                    /** @var Order $order */
                    $price = $order->cart->getTotalPrice();
                    $price = number_format($price, 0, '.', ' ') . ' ' . $mainCurrency->symbol . '<br>';
                    return $price;
                },
            ],
            [
                'label' => Yii::t(ShopModule::TRANSLATION_ORDER, 'Update time / Elapsed time'),
                'content' => function ($order) use ($currencies, $mainCurrency) {
                    /** @var Order $order */
                    if (OrderStageLog::STATUS_NEW == $order->currentStage->stage_id) {
                        $createdAt = date_create('@' . $order->currentStage->created_at);
                        $currentTime = date_create('@' . time());
                        $interval = date_diff($createdAt, $currentTime);
                        return $interval->d . ' d | ' . $interval->h . ':' . $interval->i;
                    } else {
                        return date('d-m-Y H:m', $order->currentStage->created_at);
                    }
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{to-work} {view}',
                'buttons' => [
                    'to-work' => function ($url, $model, $key) {
                        /** @var Order $model */
                        if (OrderStageLog::STATUS_NEW == $model->currentStage->stage_id) {
                            return Html::a(Yii::t(ShopModule::TRANSLATION_ORDER, 'To work'), $url, [
                                'class' => 'btn btn-success',
                            ]);
                        }
                        return '';
                    },
                    'view' => function ($url) {
                        /** @var Order $model */
                        return Html::a(Yii::t(ShopModule::TRANSLATION_ORDER, 'Order info'), $url, [
                            'class' => 'btn btn-info',
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
