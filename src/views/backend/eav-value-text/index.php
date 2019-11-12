<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use DmitriiKoziuk\yii2Shop\entities\EavValueTextEntity;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;

/* @var $this yii\web\View */
/* @var $searchModel DmitriiKoziuk\yii2Shop\entities\search\EavValueTextEntitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Eav Value Text Entities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-value-text-entity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Eav Value Text Entity', ['create'], ['class' => 'btn btn-success']) ?>
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
                    /** @var EavValueTextEntity $model */
                    return empty($model->eavAttribute) ? null : $model->eavAttribute->name;
                },
                'filter' => ArrayHelper::map(
                    EavAttributeEntity::find()->where([
                        'storage_type' => EavAttributeEntity::STORAGE_TYPE_TEXT,
                    ])->all(),
                    'id',
                    'name'
                ),
            ],
            'value:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
