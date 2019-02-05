<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use DmitriiKoziuk\yii2Shop\ShopModule;

/* @var $this yii\web\View */
/* @var $model backend\models\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sku') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'price') ?>

    <?= $form->field($model, 'old_price') ?>

    <?php // echo $form->field($model, 'purchase_price') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t(ShopModule::TRANSLATION_PRODUCT, 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
