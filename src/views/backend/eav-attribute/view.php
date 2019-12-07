<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\EavAttributeEntity */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Eav Attribute Entities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="eav-attribute-entity-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'name_for_product',
            'name_for_filter',
            'code',
            'storage_type',
            'selectable:boolean',
            'multiple:boolean',
            'view_at_frontend_faceted_navigation:boolean',
            'value_type_id',
            'default_value_type_unit_id',
        ],
    ]) ?>

</div>
