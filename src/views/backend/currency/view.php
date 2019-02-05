<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use DmitriiKoziuk\yii2Shop\ShopModule;
use DmitriiKoziuk\yii2Base\BaseModule as BaseModule;

/* @var $this yii\web\View */
/* @var $model \DmitriiKoziuk\yii2Shop\entities\Currency */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t(ShopModule::TRANSLATION_CURRENCY, 'Currencies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t(BaseModule::TRANSLATE, 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t(BaseModule::TRANSLATE, 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t(BaseModule::TRANSLATE, 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'code',
            'name',
            'symbol',
            'rate',
        ],
    ]) ?>

</div>
