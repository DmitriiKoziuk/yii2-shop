<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use DmitriiKoziuk\yii2Shop\ShopModule;

/* @var $this yii\web\View */
/* @var $model DmitriiKoziuk\yii2Shop\entities\ProductType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_PRODUCT_TYPE, 'Product types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'site_name',
            'product_title',
            'product_description',
        ],
    ]) ?>

</div>
