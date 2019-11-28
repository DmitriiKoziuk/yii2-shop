<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTypeEntity;
use DmitriiKoziuk\yii2Shop\entities\EavValueTypeUnitEntity;

/* @var $this yii\web\View */
/* @var $searchModel DmitriiKoziuk\yii2Shop\entities\search\EavAttributeEntitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Eav Attribute Entities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-attribute-entity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Eav Attribute Entity', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'name_for_product',
            'name_for_filter',
            'code',
            [
                'attribute' => 'storage_type',
                'filter' => [
                    EavAttributeEntity::STORAGE_TYPE_VARCHAR => EavAttributeEntity::STORAGE_TYPE_VARCHAR,
                    EavAttributeEntity::STORAGE_TYPE_TEXT => EavAttributeEntity::STORAGE_TYPE_TEXT,
                    EavAttributeEntity::STORAGE_TYPE_DOUBLE => EavAttributeEntity::STORAGE_TYPE_DOUBLE,
                ],
            ],
            'selectable:boolean',
            'multiple:boolean',
            [
                'attribute' => 'value_type_id',
                'label' => 'Value type',
                'content' => function ($model) {
                    /** @var EavAttributeEntity $model */
                    return empty($model->valueType) ? null : $model->valueType->name;
                },
                'filter' => ArrayHelper::map(EavValueTypeEntity::find()->all(), 'id', 'name'),
            ],
            [
                'attribute' => 'default_value_type_unit_id',
                'label' => 'Value type unit',
                'content' => function ($model) {
                    /** @var EavAttributeEntity $model */
                    return empty($model->defaultValueTypeUnit) ? null : $model->defaultValueTypeUnit->name;
                },
                'filter' => ArrayHelper::map(EavValueTypeUnitEntity::find()->all(), 'id', 'fullName', 'valueTypeName'),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
