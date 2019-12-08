<?php

use yii\helpers\Html;
use yii\grid\GridView;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Shop\entities\Category;

/**
 * @var $this         \yii\web\View
 * @var $searchModel  \DmitriiKoziuk\yii2Shop\entities\search\CategorySearch
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

$this->title = Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Create Category'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Full Tree'), ['full-tree'], ['class' => 'btn btn-default']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => Yii::t(ShopModule::TRANSLATION_CATEGORY, 'Parents'),
                'content'   => function ($model) {
                    /** @var $model Category */
                    return $model->getParentsNames();
                }
            ],
            'name',
            'slug',
            [
                'label' => 'Url',
                'content' => function ($model) {
                    /** @var $model Category */
                    return $model->urlEntity->url;
                },
            ],
            'is_products_show:boolean',
            'template_name',

            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
