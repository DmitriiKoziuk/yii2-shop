<?php

use yii\helpers\Html;
use yii\grid\GridView;
use DmitriiKoziuk\yii2Shop\ShopModule;

/* @var $this yii\web\View */
/* @var $searchModel DmitriiKoziuk\yii2Shop\entities\search\SupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t(ShopModule::TRANSLATION_SUPPLIER, 'Suppliers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t(ShopModule::TRANSLATION_SUPPLIER, 'Create Supplier'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'phone_number',
            'email:email',
            'info',
            'created_at:datetime',
            'updated_at:datetime',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{create-price} {view} {update} {delete}',
                'buttons'  => [
                    'create-price' => function ($url, $model) {
                        /** @var \DmitriiKoziuk\yii2Shop\entities\Supplier $model */
                        return Html::a(
                            '<span class="glyphicon glyphicon-plus-sign"></span>',
                            \yii\helpers\Url::to(['supplier-price/create', 'supplier_id' => $model->id])
                        );
                    }
                ],
            ],
        ],
    ]); ?>
</div>
