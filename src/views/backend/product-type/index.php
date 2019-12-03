<?php

use yii\web\View;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\entities\ProductType;
use DmitriiKoziuk\yii2Shop\entities\search\ProductTypeSearch;

/**
 * @var $this         View
 * @var $searchModel  ProductTypeSearch
 * @var $dataProvider ActiveDataProvider
 */

$this->title = Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Product types');
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Products'), 'url' => ['product/index']];
$this->params['breadcrumbs'][] = Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Product types');
?>
<div class="product-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Create product type'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'label' => 'Related EAV Attributes',
                'content' => function ($model) {
                    /** @var $model ProductType */
                    $r = '';
                    foreach ($model->eavAttributeEntities as $attribute) {
                        $r .= Html::tag('div', $attribute->name);
                    }
                    return $r;
                },
            ],
            'name_on_site',
            'margin_strategy',

            [
                'attribute' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Product number'),
                'content'   => function ($model) {
                    /** @var $model ProductType */
                    return $model->getProductNumber();
                },
            ],

            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update-margin} {view} {update} {delete}',
                'buttons'  => [
                    'update-margin' => function ($url) {
                        return Html::a('<span class="glyphicon glyphicon-usd"></span>', $url);
                    }
                ]
            ],
        ],
    ]); ?>
</div>
