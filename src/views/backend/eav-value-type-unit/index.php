<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel DmitriiKoziuk\yii2Shop\entities\search\EavValueTypeUnitEntitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Eav Value Type Unit Entities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eav-value-type-unit-entity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Eav Value Type Unit Entity', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'value_type_id',
            'name',
            'code',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
