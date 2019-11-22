<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use DmitriiKoziuk\yii2Shop\entities\EavValueDoubleEntity;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;

/* @var $this yii\web\View */
/* @var $searchModel DmitriiKoziuk\yii2Shop\entities\search\EavValueDoubleEntitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Eav Value Double Entities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-value-double-entity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Eav Value Double Entity', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'attribute_id',
                'label' => 'Attribute',
                'content' => function ($model) {
                    /** @var EavValueDoubleEntity $model */
                    return empty($model->eavAttribute) ? null : $model->eavAttribute->name;
                },
                'filter' => ArrayHelper::map(
                    EavAttributeEntity::find()->where([
                        'storage_type' => EavAttributeEntity::STORAGE_TYPE_DOUBLE,
                    ])->all(),
                    'id',
                    'name'
                ),
            ],
            'value',
            'code',
            [
                'attribute' => 'value_type_unit_id',
                'label' => 'Unit',
                'content' => function ($model) {
                    /** @var EavValueDoubleEntity $model */
                    return empty($model->unit) ? null : "{$model->unit->name} ({$model->unit->abbreviation})";
                },
            ],
            [
                'label' => 'Product sku number',
                'content' => function ($model) {
                    /** @var EavValueDoubleEntity $model */
                    return $model->getRelatedProductSkuNumber();
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
