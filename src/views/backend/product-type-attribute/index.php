<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use DmitriiKoziuk\yii2Shop\entities\ProductType;
use DmitriiKoziuk\yii2Shop\entities\ProductTypeAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;

/* @var $this yii\web\View */
/* @var $searchModel DmitriiKoziuk\yii2Shop\entities\search\ProductTypeAttributeEntitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Type Attribute Entities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-type-attribute-entity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Type Attribute Entity', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'product_type_id',
                'label' => 'Product type',
                'content' => function ($model) {
                    /** @var ProductTypeAttributeEntity $model */
                    return $model->productType->name;
                },
                'filter' => ArrayHelper::map(ProductType::find()->all(), 'id', 'name'),
            ],
            [
                'attribute' => 'attribute_id',
                'label' => 'Attribute',
                'content' => function ($model) {
                    /** @var ProductTypeAttributeEntity $model */
                    return $model->attributeData->name;
                },
                'filter' => ArrayHelper::map(EavAttributeEntity::find()->all(), 'id', 'name'),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
