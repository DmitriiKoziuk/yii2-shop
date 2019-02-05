<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use DmitriiKoziuk\yii2FileManager\repositories\FileRepository;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\Currency;
use DmitriiKoziuk\yii2Shop\helpers\CategoryHelper;

/**
 * @var $this             yii\web\View
 * @var $dataProvider     yii\data\ActiveDataProvider
 * @var $productTypes     \DmitriiKoziuk\yii2Shop\entities\ProductType[]
 * @var $productSkuSearch \DmitriiKoziuk\yii2Shop\entities\search\ProductSkuSearch
 * @var $categories       \DmitriiKoziuk\yii2Shop\entities\Category[]
 * @var $fileRepository   FileRepository
 * @var $fileWebHelper    FileWebHelper
 */

$this->title = Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(
            Yii::t(
                ShopModule::TRANSLATION_PRODUCT,
                'Create Product'),
            ['create'],
            ['class' => 'btn btn-success']
        ) ?>
        <?= Html::a(
            Yii::t(
                ShopModule::TRANSLATION_PRODUCT_TYPE,
                'Create product type'),
            ['product-type/create'],
            ['class' => 'btn btn-success']
        ) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $productSkuSearch,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'Type name',
                'content'   => function ($model) {
                    /** @var ProductSku $model */
                    return $model->getTypeName();
                },
                'filter'    => Select2::widget([
                    'model'     => $productSkuSearch,
                    'attribute' => 'type_id',
                    'data'      => ArrayHelper::map($productTypes, 'id', 'name'),
                    'options'   => [
                        'placeholder' => 'Select a Type ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]),
            ],
            [
                'attribute' => 'Category name',
                'content'   => function ($model) {
                    /** @var ProductSku $model */
                    return $model->getCategoryName();
                },
                'filter'    => Select2::widget([
                    'model' => $productSkuSearch,
                    'attribute' => 'category_id',
                    'data' => ArrayHelper::map(
                        CategoryHelper::categoryTreeToList(
                            CategoryHelper::createCategoryTree($categories)
                        ),
                        'id',
                        'name'
                    ),
                    'options' => [
                        'placeholder' => 'Select a category ...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])
            ],
            [
                'attribute' => 'stock_status',
                'content'   => function ($model) {
                    /** @var $model ProductSku */
                    return ProductSku::getStockVariation($model->stock_status);
                },
                'filter' => ProductSku::getStockVariation(),
            ],
            [
                'attribute' => 'image',
                'content'   => function ($model) use ($fileRepository, $fileWebHelper) {
                    /** @var $model ProductSku */
                    $images = $fileRepository->getEntityImages($model::FILE_ENTITY_NAME, $model->id);
                    if (! empty($images)) {
                        return Html::tag(
                            'div',
                            Html::img(
                                $fileWebHelper->getFileFullWebPath($images[1]),
                                ['style' => 'max-width: 150px;max-height: 150px;']
                            ),
                            ['style' => 'text-align: center;']
                        );
                    }
                    return null;
                }
            ],
            [
                'attribute' => 'product_name',
                'content'   => function ($model) {
                    /** @var $model \DmitriiKoziuk\yii2Shop\entities\ProductSku */
                    return $model->product->name;
                },
            ],
            [
                'attribute' => 'sku_name',
                'content'   => function ($model) {
                    /** @var $model \DmitriiKoziuk\yii2Shop\entities\ProductSku */
                    return $model->name;
                },
            ],
            [
                'attribute' => 'currency_id',
                'content'   => function ($model) {
                    /** @var $model ProductSku */
                    return $model->getCurrencyCode();
                },
                'filter'    => ArrayHelper::map(Currency::find()->all(), 'id', 'code')
            ],
            'sell_price',
            'old_price',
            'price_on_site',

            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons'  => [
                    'update' => function ($url, $model) {
                        /** @var $model ProductSku */
                        return Html::a(
                            '<span class="glyphicon glyphicon-edit"></span>',
                            \yii\helpers\Url::to(['product/update', 'id' => $model->product_id])
                        );
                    }
                ],
            ],
        ],
    ]); ?>
</div>
