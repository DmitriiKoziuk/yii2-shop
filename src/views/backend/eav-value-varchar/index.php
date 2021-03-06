<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use DmitriiKoziuk\yii2Shop\entities\EavValueVarcharEntity;
use DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity;

/* @var $this yii\web\View */
/* @var $searchModel DmitriiKoziuk\yii2Shop\entities\search\EavValueVarcharEntitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Eav Value Varchar Entities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-value-varchar-entity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Eav Value Varchar Entity', ['create'], ['class' => 'btn btn-success']) ?>
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
                    /** @var EavValueVarcharEntity $model */
                    return empty($model->eavAttribute) ? null : $model->eavAttribute->name;
                },
                'filter' => ArrayHelper::map(
                    EavAttributeEntity::find()->where([
                        'storage_type' => EavAttributeEntity::STORAGE_TYPE_VARCHAR,
                    ])->all(),
                    'id',
                    'name'
                ),
            ],
            'value',
            'code',
            [
                'label' => 'Product sku number',
                'content' => function ($model) {
                    /** @var EavValueVarcharEntity $model */
                    return $model->getRelatedProductSkuNumber();
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {view} {delete} {seo}',
                'buttons'  => [
                    'seo' => function ($url, $model) {
                        /** @var $model EavValueVarcharEntity */
                        if ($model->eavAttribute->selectable) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-th-list"></span>',
                                $url
                            );
                        }
                        return '';
                    }
                ],
            ],
        ],
    ]); ?>


</div>
