<?php

use yii\helpers\Html;
use yii\grid\GridView;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\entities\ProductTypeMargin;

/**
 * @var $this         yii\web\View
 * @var $searchModel  \DmitriiKoziuk\yii2Shop\entities\search\ProductTypeSearch
 * @var $dataProvider yii\data\ActiveDataProvider
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
            'name_on_site',
            [
                'attribute' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Margin'),
                'content'   => function ($model) {
                    /** @var $model \DmitriiKoziuk\yii2Shop\entities\ProductType */
                    $content = '';
                    /** @var ProductTypeMargin $margin */
                    foreach ($model->margins as $margin) {
                        $marginSymbol = $margin->type === ProductTypeMargin::MARGIN_TYPE_PERCENT ?
                            '%' : $margin->currency->symbol;
                        $content .= Html::tag(
                            'div',
                            $margin->currency->code . ' + ' . $margin->value . ' ' . $marginSymbol
                        );
                    }
                    return $content;
                },
            ],
            [
                'attribute' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Product number'),
                'content'   => function ($model) {
                    /** @var $model \DmitriiKoziuk\yii2Shop\entities\ProductType */
                    return $model->getProductNumber();
                },
            ],

            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{margin} {view} {update} {delete}',
                'buttons'  => [
                    'margin' => function ($url) {
                        return Html::a('<span class="glyphicon glyphicon-usd"></span>', $url);
                    }
                ]
            ],
        ],
    ]); ?>
</div>
